<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

/**
 * Copia os ficheiros que ainda estão em storage/app/public e
 * storage/app/private para os buckets R2 configurados. Constrói os discos
 * de destino directamente a partir das variáveis de ambiente (em vez de
 * usar os discos "public"/"private" do config/filesystems.php), para poder
 * correr ANTES de esse ficheiro ser actualizado para apontar para o R2 —
 * assim os dados já estão no R2 quando a mudança de configuração for feita,
 * sem haver uma janela em que imagens/documentos ficam inacessíveis.
 */
class MigrateStorageToR2 extends Command
{
    protected $signature = 'storage:migrate-to-r2
        {--delete : Apaga o ficheiro local depois de confirmar que a cópia no R2 tem o mesmo tamanho}
        {--dry-run : Só lista o que seria migrado, sem copiar nada}';

    protected $description = 'Copia storage/app/public e storage/app/private para os buckets R2 (AWS_BUCKET e AWS_BUCKET_PRIVATE)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $delete = (bool) $this->option('delete');

        $targets = [
            'public'  => ['root' => storage_path('app/public'), 'bucket_env' => 'AWS_BUCKET'],
            'private' => ['root' => storage_path('app/private'), 'bucket_env' => 'AWS_BUCKET_PRIVATE'],
        ];

        foreach ($targets as $label => $target) {
            $this->info("── {$label} ──");

            if (!is_dir($target['root'])) {
                $this->line('  pasta local não existe, nada a migrar.');
                continue;
            }

            $bucket = env($target['bucket_env']);
            if (!$bucket) {
                $this->error("  {$target['bucket_env']} não está definido no .env — a saltar.");
                continue;
            }

            $disk = $this->remoteDisk($bucket);

            $ok = 0;
            $failed = 0;
            $count = 0;

            foreach (Finder::create()->files()->in($target['root']) as $file) {
                $count++;
                $relativePath = str_replace('\\', '/', $file->getRelativePathname());
                $localSize = $file->getSize();

                if ($dryRun) {
                    $this->line("  migraria: {$relativePath} ({$localSize} bytes)");
                    continue;
                }

                try {
                    $stream = fopen($file->getRealPath(), 'r');
                    $disk->put($relativePath, $stream);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }

                    $remoteSize = $disk->size($relativePath);
                    if ($remoteSize !== $localSize) {
                        $this->error("  FALHOU (tamanho diferente {$localSize} vs {$remoteSize}): {$relativePath}");
                        $failed++;
                        continue;
                    }

                    $ok++;

                    if ($delete) {
                        @unlink($file->getRealPath());
                    }
                } catch (\Throwable $e) {
                    $this->error("  FALHOU: {$relativePath} — {$e->getMessage()}");
                    $failed++;
                }
            }

            if ($dryRun) {
                $this->info("  total encontrado: {$count}");
            } else {
                $this->info("  total: {$count} | copiados: {$ok} | falhas: {$failed}");
            }
        }

        return self::SUCCESS;
    }

    private function remoteDisk(string $bucket): Filesystem
    {
        return Storage::build([
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => $bucket,
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => filter_var(env('AWS_USE_PATH_STYLE_ENDPOINT', true), FILTER_VALIDATE_BOOLEAN),
            'throw' => true,
        ]);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

/**
 * Copia os ficheiros que ainda estão em storage/app/public e
 * storage/app/private para os discos "public"/"private" configurados em
 * config/filesystems.php (que já apontam para os buckets R2). Usa
 * config()/Storage::disk() em vez de env() directo — depois de um
 * `config:cache`, o Laravel deixa de carregar o .env, e qualquer env()
 * fora dos ficheiros de config passaria sempre a devolver null.
 */
class MigrateStorageToR2 extends Command
{
    protected $signature = 'storage:migrate-to-r2
        {--delete : Apaga o ficheiro local depois de confirmar que a cópia no R2 tem o mesmo tamanho}
        {--dry-run : Só lista o que seria migrado, sem copiar nada}';

    protected $description = 'Copia storage/app/public e storage/app/private para os discos "public"/"private" (R2)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $delete = (bool) $this->option('delete');

        $targets = [
            'public'  => storage_path('app/public'),
            'private' => storage_path('app/private'),
        ];

        foreach ($targets as $diskName => $root) {
            $this->info("── {$diskName} ──");

            if (!is_dir($root)) {
                $this->line('  pasta local não existe, nada a migrar.');
                continue;
            }

            $bucket = config("filesystems.disks.{$diskName}.bucket");
            if (!$bucket) {
                $this->error("  config(filesystems.disks.{$diskName}.bucket) está vazio — confirma o .env e corre php artisan config:cache de novo.");
                continue;
            }

            $disk = Storage::disk($diskName);

            $ok = 0;
            $failed = 0;
            $count = 0;

            foreach (Finder::create()->files()->in($root) as $file) {
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
}

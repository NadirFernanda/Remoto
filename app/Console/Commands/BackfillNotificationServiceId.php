<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

/**
 * Backfills service_id for old notifications where service_id IS NULL.
 *
 * These notifications were created before the service_id column was added,
 * or via older code paths that didn't store it.
 *
 * Strategy:
 *  - "novo_projeto" messages contain the project title after the prefix
 *    "Um novo projeto foi publicado: {titulo}" — we extract and match.
 *  - Other types (nova_mensagem, delivery_submitted, etc.) can sometimes
 *    be correlated by user + message text.
 *
 * Usage:
 *   php artisan notifications:backfill-service-id --dry-run
 *   php artisan notifications:backfill-service-id
 */
class BackfillNotificationServiceId extends Command
{
    protected $signature = 'notifications:backfill-service-id {--dry-run : Preview without writing}';
    protected $description = 'Backfill service_id on notifications that have service_id = NULL';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $fixed  = 0;
        $skipped = 0;

        if ($dryRun) {
            $this->warn('[DRY-RUN] No changes will be written.');
        }

        // Build a title→id lookup (most recent service wins if titles collide)
        $titleMap = Service::withTrashed()
            ->select('id', 'titulo')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn($s) => [mb_strtolower(trim($s->titulo)) => $s->id])
            ->all();

        DB::table('user_notifications')
            ->whereNull('service_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($dryRun, &$fixed, &$skipped, $titleMap) {
                foreach ($rows as $row) {
                    $sid = $this->resolveServiceId($row, $titleMap);

                    if (!$sid) {
                        $skipped++;
                        continue;
                    }

                    $this->line("  ID {$row->id} [{$row->type}] -> service #{$sid}  \"{$row->message}\"");

                    $fixed++;
                    if (!$dryRun) {
                        DB::table('user_notifications')
                            ->where('id', $row->id)
                            ->update(['service_id' => $sid]);
                    }
                }
            });

        $this->info(($dryRun ? '[DRY-RUN] Would fix' : 'Fixed') . " {$fixed} notifications. Skipped (unresolvable): {$skipped}.");

        return self::SUCCESS;
    }

    private function resolveServiceId(object $row, array $titleMap): ?int
    {
        $message = $row->message ?? '';
        $type    = $row->type   ?? '';

        // "Um novo projeto foi publicado: TITULO"
        if ($type === 'novo_projeto') {
            $prefix = 'Um novo projeto foi publicado: ';
            if (str_starts_with($message, $prefix)) {
                $title = mb_strtolower(trim(mb_substr($message, mb_strlen($prefix))));
                return $titleMap[$title] ?? null;
            }
        }

        // Generic fallback: look for any title match in the message
        foreach ($titleMap as $title => $id) {
            if ($title !== '' && mb_stripos($message, $title) !== false) {
                return $id;
            }
        }

        return null;
    }
}

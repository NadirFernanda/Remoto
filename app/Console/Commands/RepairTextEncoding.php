<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

/**
 * Scans all services and repairs text columns that were stored with
 * double-encoded UTF-8 (e.g. "ó" appears as "Ã³").
 *
 * Root cause: a prior database connection with client_encoding ≠ UTF8
 * caused PostgreSQL to re-encode already-correct UTF-8 bytes as if
 * they were Latin-1, producing double-encoded stored values.
 *
 * Safe to run multiple times: already-correct rows are left untouched.
 *
 * Usage:
 *   php artisan text:repair-encoding          # apply fixes
 *   php artisan text:repair-encoding --dry-run # preview without writing
 */
class RepairTextEncoding extends Command
{
    protected $signature = 'text:repair-encoding {--dry-run : Preview changes without writing to DB}';
    protected $description = 'Repair double-encoded UTF-8 text in service columns (titulo, briefing, descricao)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $fixed  = 0;
        $total  = 0;

        if ($dryRun) {
            $this->warn('[DRY-RUN] No changes will be written.');
        }

        Service::withTrashed()->chunkById(200, function ($services) use ($dryRun, &$fixed, &$total) {
            foreach ($services as $service) {
                $total++;
                $changed = false;

                $columns = ['titulo', 'briefing', 'descricao'];
                $updates = [];

                foreach ($columns as $col) {
                    $raw = $service->getRawOriginal($col);
                    if ($raw === null) continue;

                    $repaired = Service::fixDoubleEncodedUtf8($raw);
                    if ($repaired !== $raw) {
                        $updates[$col] = $repaired;
                        $changed = true;
                        $this->line("  ID {$service->id} [{$col}]: " . mb_substr($raw, 0, 60) . ' → ' . mb_substr($repaired, 0, 60));
                    }
                }

                if ($changed) {
                    $fixed++;
                    if (!$dryRun) {
                        // Use DB::table to bypass accessors/mutators and avoid touching timestamps
                        DB::table('services')->where('id', $service->id)->update($updates);
                    }
                }
            }
        });

        $this->info("Checked {$total} services. " . ($dryRun ? "Would fix" : "Fixed") . " {$fixed} rows.");

        return self::SUCCESS;
    }
}

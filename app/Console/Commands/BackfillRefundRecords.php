<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\Service;
use App\Models\Refund;

/**
 * Creates missing Refund records for old notifications of type
 * refund_processed / refund_approved that were dispatched before
 * commit 0345e6a (which added Refund::create() in DisputeAdmin).
 *
 * Safe to run multiple times — skips services that already have a refund.
 *
 * Usage:
 *   php artisan refunds:backfill --dry-run
 *   php artisan refunds:backfill
 */
class BackfillRefundRecords extends Command
{
    protected $signature = 'refunds:backfill {--dry-run : Preview without writing}';
    protected $description = 'Create missing Refund records for old refund notifications';

    public function handle(): int
    {
        $dryRun  = (bool) $this->option('dry-run');
        $created = 0;
        $skipped = 0;

        if ($dryRun) {
            $this->warn('[DRY-RUN] No changes will be written.');
        }

        // Find all refund notifications that have a service_id
        Notification::whereIn('type', ['refund_processed', 'refund_approved'])
            ->whereNotNull('service_id')
            ->orderBy('id')
            ->chunkById(100, function ($notifications) use ($dryRun, &$created, &$skipped) {
                foreach ($notifications as $notif) {
                    $alreadyExists = Refund::where('user_id', $notif->user_id)
                        ->where('service_id', $notif->service_id)
                        ->exists();

                    if ($alreadyExists) {
                        $skipped++;
                        continue;
                    }

                    $service = Service::withTrashed()->find($notif->service_id);
                    if (!$service) {
                        $skipped++;
                        continue;
                    }

                    $valor = $service->valor ?? 0;
                    $this->line("  Criar Refund: user #{$notif->user_id} service #{$notif->service_id} ({$notif->type})");

                    $created++;
                    if (!$dryRun) {
                        Refund::create([
                            'user_id'    => $notif->user_id,
                            'service_id' => $notif->service_id,
                            'reason'     => 'Disputa resolvida a favor do cliente',
                            'details'    => 'Reembolso de ' . number_format($valor, 0, ',', '.') . ' Kz (recuperado a partir de notificação).',
                            'status'     => 'approved',
                        ]);
                    }
                }
            });

        $this->info(($dryRun ? '[DRY-RUN] Criaria' : 'Criados') . " {$created} registos. Ignorados: {$skipped}.");

        return self::SUCCESS;
    }
}

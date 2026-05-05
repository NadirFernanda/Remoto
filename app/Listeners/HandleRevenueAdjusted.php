<?php

namespace App\Listeners;

use App\Events\RevenueAdjusted;
use App\Modules\Admin\Services\AuditLogger;
use App\Notifications\RevenueAdjustedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class HandleRevenueAdjusted implements ShouldQueue
{
    use Queueable;

    public function handle(RevenueAdjusted $event): void
    {
        // Log da auditoria
        AuditLogger::log(
            'revenue_adjusted',
            "Receita ajustada em {$event->amount} Kz para o usuário \"{$event->user->name}\". Motivo: {$event->reason}",
            'User',
            $event->user->id
        );

        // Notificar o usuário
        $event->user->notify(new RevenueAdjustedNotification($event->amount, $event->reason));
    }
}
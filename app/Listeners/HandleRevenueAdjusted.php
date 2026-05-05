<?php

namespace App\Listeners;

use App\Events\RevenueAdjusted;
use App\Models\Notification;
use App\Modules\Admin\Services\AuditLogger;
use App\Notifications\RevenueAdjustedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleRevenueAdjusted implements ShouldQueue
{
    use Queueable;

    public function handle(RevenueAdjusted $event): void
    {
        AuditLogger::log(
            'revenue_adjusted',
            "Receita ajustada em {$event->amount} Kz para o utilizador \"{$event->user->name}\". Motivo: {$event->reason}",
            'User',
            $event->user->id
        );

        // Notificação in-app via modelo customizado do projecto
        Notification::create([
            'user_id' => $event->user->id,
            'type'    => 'revenue_adjusted',
            'title'   => $event->amount >= 0 ? 'Receita actualizada' : 'Ajuste de receita',
            'message' => 'A tua receita foi ajustada em '
                . ($event->amount >= 0 ? '+' : '')
                . number_format($event->amount, 0, ',', '.') . ' Kz pelo admin.'
                . ' Motivo: ' . $event->reason,
        ]);

        // Email ao utilizador
        $event->user->notify(new RevenueAdjustedNotification($event->amount, $event->reason));
    }
}

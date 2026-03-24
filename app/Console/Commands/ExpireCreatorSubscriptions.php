<?php

namespace App\Console\Commands;

use App\Models\CreatorSubscription;
use App\Models\Notification;
use Illuminate\Console\Command;

/**
 * Artisan command: processa expiração de subscrições de criadores.
 *
 * Responsabilidades:
 *  1. Marcar como 'expired' todas as subscrições cujo expires_at é passado.
 *  2. Notificar subscritores 3 dias antes da expiração (lembrete de renovação).
 *
 * Agendamento recomendado: diariamente às 02:00 (Kernel.php).
 * Execução manual: php artisan subscriptions:expire
 */
class ExpireCreatorSubscriptions extends Command
{
    protected $signature   = 'subscriptions:expire';
    protected $description = 'Expirar subscrições de criadores vencidas e enviar lembretes de renovação';

    public function handle(): int
    {
        $expiredCount  = $this->expireOverdue();
        $reminderCount = $this->sendExpiryReminders();

        $this->info("Expiradas: {$expiredCount} | Lembretes enviados: {$reminderCount}");
        return self::SUCCESS;
    }

    /**
     * Marca como 'expired' todas as subscrições activas já vencidas.
     */
    private function expireOverdue(): int
    {
        $count = CreatorSubscription::query()
            ->where('status', 'active')
            ->where('expires_at', '<=', now())
            ->count();

        CreatorSubscription::query()
            ->where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        return $count;
    }

    /**
     * Notifica subscritores cuja subscrição expira nos próximos 3 dias.
     * Evita duplicar notificações consultando notificações já enviadas hoje.
     */
    private function sendExpiryReminders(): int
    {
        $soon = now()->addDays(3);
        $sent = 0;

        CreatorSubscription::query()
            ->where('status', 'active')
            ->whereBetween('expires_at', [now(), $soon])
            ->with('subscriber', 'creator')
            ->chunkById(50, function ($subscriptions) use (&$sent) {
                foreach ($subscriptions as $sub) {
                    // Verificar se o lembrete já foi enviado hoje
                    $alreadySent = Notification::query()
                        ->where('user_id', $sub->subscriber_id)
                        ->where('type', 'subscription_expiry_reminder')
                        ->whereDate('created_at', today())
                        ->exists();

                    if ($alreadySent || !$sub->subscriber) {
                        continue;
                    }

                    Notification::create([
                        'user_id' => $sub->subscriber_id,
                        'type'    => 'subscription_expiry_reminder',
                        'title'   => 'Subscrição a expirar em breve',
                        'message' => 'A sua subscrição a "' . ($sub->creator?->name ?? 'criador') . '" expira em ' . $sub->expires_at->diffForHumans() . '.',
                        'read'    => false,
                    ]);

                    $sent++;
                }
            });

        return $sent;
    }
}

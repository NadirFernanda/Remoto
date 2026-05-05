<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Jobs\RetryPaymentJob;
use App\Models\Notification;
use App\Modules\Admin\Services\AuditLogger;
use App\Notifications\PaymentFailedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandlePaymentFailure implements ShouldQueue
{
    use Queueable;

    public function handle(PaymentFailed $event): void
    {
        if (!$event->user) {
            return;
        }

        AuditLogger::log(
            'payment_failed',
            "Falha no pagamento de {$event->amount} Kz pelo utilizador \"{$event->user->name}\". Motivo: {$event->reason}",
            $event->service ? 'Service' : 'User',
            $event->service ? $event->service->id : $event->user->id
        );

        // Notificação in-app via modelo customizado do projecto
        Notification::create([
            'user_id' => $event->user->id,
            'type'    => 'payment_failed',
            'title'   => 'Falha no pagamento',
            'message' => 'O teu pagamento de ' . number_format($event->amount, 0, ',', '.') . ' Kz falhou. '
                . 'Motivo: ' . $event->reason . '. Vamos tentar novamente em 1 hora.',
        ]);

        // Email imediato ao utilizador
        $event->user->notify(new PaymentFailedNotification($event->amount, $event->reason));

        // Agendar retry automático se existir um serviço associado
        if ($event->service) {
            RetryPaymentJob::dispatch(
                $event->service,
                $event->user,
                $event->amount,
                [] // paymentData vazio: gateway de retry usa o token guardado em sessão ou requer nova autorização
            )
            ->delay(now()->addHour())
            ->onQueue('payments');
        }
    }
}

<?php

namespace App\Notifications;

use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRetryFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service,
        public User $user,
        public float $amount,
        public string $reason
    ) {}

    public function via($notifiable): array
    {
        // Apenas email — a in-app é criada directamente em RetryPaymentJob
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ALERTA] Falha definitiva de pagamento — ' . $this->service->titulo)
            ->greeting('Admin ' . $notifiable->name . ',')
            ->line('Um pagamento falhou definitivamente após todas as tentativas de retry.')
            ->line('**Utilizador:** ' . $this->user->name . ' (' . $this->user->email . ')')
            ->line('**Serviço:** ' . $this->service->titulo . ' (ID: #' . $this->service->id . ')')
            ->line('**Valor:** ' . number_format($this->amount, 0, ',', '.') . ' Kz')
            ->line('**Motivo:** ' . $this->reason)
            ->action('Ver serviço no admin', route('admin.services'))
            ->line('Intervenção manual pode ser necessária.');
    }
}

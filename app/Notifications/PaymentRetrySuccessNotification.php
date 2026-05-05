<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRetrySuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service,
        public float $amount
    ) {}

    public function via($notifiable): array
    {
        // Apenas email — a in-app é criada directamente em RetryPaymentJob
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pagamento processado com sucesso')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('O pagamento que havia falhado foi processado com sucesso na segunda tentativa.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Valor:** ' . number_format($this->amount, 0, ',', '.') . ' Kz')
            ->action('Ver os meus pedidos', route('client.orders'))
            ->line('O teu pedido foi publicado e os freelancers foram notificados.');
    }
}

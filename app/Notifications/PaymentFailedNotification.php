<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $amount,
        public string $reason
    ) {}

    public function via($notifiable): array
    {
        // Apenas email — a notificação in-app é criada directamente em HandlePaymentFailure
        // via App\Models\Notification para ser compatível com o centro de notificações do projecto
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Falha no processamento do pagamento')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Infelizmente, houve uma falha no processamento do seu pagamento.')
            ->line('**Valor:** ' . number_format($this->amount, 0, ',', '.') . ' Kz')
            ->line('**Motivo:** ' . $this->reason)
            ->line('Por favor, verifica os dados do cartão ou tenta outro método de pagamento.')
            ->action('Tentar Novamente', route('client.payment'))
            ->line('Se o problema persistir, entra em contacto com o suporte.');
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Service;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service,
        public float $amount,
        public string $walletUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'amount'     => $this->amount,
            'message'    => 'Pagamento de Kz ' . number_format($this->amount, 2, ',', '.') . ' creditado na sua carteira pelo projeto "' . $this->service->titulo . '".',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pagamento recebido na sua carteira')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('O cliente aprovou o projeto e libertou o pagamento.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Valor creditado:** Kz ' . number_format($this->amount, 2, ',', '.'))
            ->action('Ver Carteira', $this->walletUrl)
            ->line('O valor já está disponível na sua carteira. Pode solicitar levantamento a qualquer momento.');
    }
}

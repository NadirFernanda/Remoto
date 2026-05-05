<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RevenueAdjustedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $amount,
        public string $reason
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'amount' => $this->amount,
            'reason' => $this->reason,
            'message' => 'Sua receita foi ajustada em Kz ' . number_format($this->amount, 2, ',', '.') . '. Motivo: ' . $this->reason,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ajuste na sua receita')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Sua receita foi ajustada pelo administrador.')
            ->line('**Valor ajustado:** Kz ' . number_format($this->amount, 2, ',', '.'))
            ->line('**Motivo:** ' . $this->reason)
            ->action('Ver Perfil', url('/freelancer/profile'))
            ->line('Se tiver dúvidas, entre em contato com o suporte.');
    }
}
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RevenueAdjustedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $amount,
        public string $reason
    ) {}

    public function via($notifiable): array
    {
        // Apenas email — a in-app é criada directamente em HandleRevenueAdjusted
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sinal    = $this->amount >= 0 ? '+' : '';
        $valorFmt = $sinal . number_format($this->amount, 0, ',', '.') . ' Kz';

        return (new MailMessage)
            ->subject('Ajuste na tua receita')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('A tua receita foi ajustada pelo administrador da plataforma.')
            ->line('**Valor ajustado:** ' . $valorFmt)
            ->line('**Motivo:** ' . $this->reason)
            ->action('Ver painel financeiro', route('freelancer.financial'))
            ->line('Se tiveres dúvidas, entra em contacto com o suporte.');
    }
}

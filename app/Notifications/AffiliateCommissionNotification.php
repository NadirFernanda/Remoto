<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AffiliateCommissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public float $commission,
        public string $referredName,
        public string $affiliateUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'commission'    => $this->commission,
            'referred_name' => $this->referredName,
            'message'       => 'Ganhou uma comissão de Kz ' . number_format($this->commission, 2, ',', '.') . ' pelo registo do seu referido ' . $this->referredName . '.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Comissão de afiliado creditada!')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('O seu referido **' . $this->referredName . '** completou uma acção elegível.')
            ->line('**Comissão creditada:** Kz ' . number_format($this->commission, 2, ',', '.'))
            ->action('Ver Painel de Afiliados', $this->affiliateUrl)
            ->line('Continue a partilhar o seu link de afiliado para ganhar mais comissões!');
    }
}

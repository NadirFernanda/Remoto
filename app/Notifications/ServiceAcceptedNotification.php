<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Service;

class ServiceAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service,
        public string $serviceUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'message'    => 'A sua proposta para "' . $this->service->titulo . '" foi aceite! O projeto está em andamento.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Proposta aceite — projeto em andamento')
            ->greeting('Parabéns, ' . $notifiable->name . '!')
            ->line('A sua proposta foi aceite pelo cliente.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Valor:** Kz ' . number_format($this->service->valor, 2, ',', '.'))
            ->action('Aceder ao Projeto', $this->serviceUrl)
            ->line('Dê o seu melhor e entregue dentro do prazo combinado. Bom trabalho!');
    }
}

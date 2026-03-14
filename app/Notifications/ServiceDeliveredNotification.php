<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Service;
use App\Models\User;

class ServiceDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service,
        public User $freelancer,
        public string $serviceUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'service_id'    => $this->service->id,
            'freelancer_id' => $this->freelancer->id,
            'message'       => 'O freelancer ' . $this->freelancer->name . ' marcou o projeto "' . $this->service->titulo . '" como entregue.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Entrega recebida — reveja e aprove o seu projeto')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('O freelancer **' . $this->freelancer->name . '** submeteu a entrega do seu projeto.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->action('Rever Entrega', $this->serviceUrl)
            ->line('Acesse o projeto, reveja o trabalho entregue e aprove ou solicite revisões.');
    }
}

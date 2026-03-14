<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Service;
use App\Models\User;

class ProposalReceivedNotification extends Notification implements ShouldQueue
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
            'service_id'  => $this->service->id,
            'freelancer_id' => $this->freelancer->id,
            'message'     => 'O freelancer ' . $this->freelancer->name . ' candidatou-se ao seu projeto "' . $this->service->titulo . '".',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nova candidatura no seu projeto')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('O freelancer **' . $this->freelancer->name . '** candidatou-se ao seu projeto.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->action('Ver Candidatura', $this->serviceUrl)
            ->line('Acesse o painel para analisar a proposta e escolher o melhor profissional.');
    }
}

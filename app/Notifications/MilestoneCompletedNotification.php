<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Milestone;
use App\Models\Service;

class MilestoneCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Milestone $milestone,
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
            'service_id'   => $this->service->id,
            'milestone_id' => $this->milestone->id,
            'message'      => 'A etapa "' . $this->milestone->titulo . '" do projeto "' . $this->service->titulo . '" foi concluída.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Etapa do projeto concluída')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Uma etapa do seu projeto foi marcada como concluída.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Etapa:** ' . $this->milestone->titulo)
            ->action('Ver Projeto', $this->serviceUrl)
            ->line('Acompanhe o progresso completo do projeto no painel.');
    }
}

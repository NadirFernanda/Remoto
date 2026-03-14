<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Service;
use App\Models\User;

class ServiceCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Service $service,
        public User $cancelledBy,
        public string $dashboardUrl,
        public ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'message'    => 'O projeto "' . $this->service->titulo . '" foi cancelado por ' . $this->cancelledBy->name . '.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Projeto cancelado')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Informamos que o projeto abaixo foi cancelado.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Cancelado por:** ' . $this->cancelledBy->name)
            ->when($this->reason, fn($mail) => $mail->line('**Motivo:** ' . $this->reason))
            ->action('Ver Dashboard', $this->dashboardUrl)
            ->line('Se tiver alguma dúvida, pode contactar o suporte ou abrir uma disputa.');
    }
}

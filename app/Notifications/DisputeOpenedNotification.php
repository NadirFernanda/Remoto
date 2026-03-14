<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Dispute;
use App\Models\Service;
use App\Models\User;

class DisputeOpenedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Dispute $dispute,
        public Service $service,
        public User $openedBy,
        public string $disputeUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'dispute_id' => $this->dispute->id,
            'message'    => $this->openedBy->name . ' abriu uma disputa no projeto "' . $this->service->titulo . '".',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $reasonLabels = [
            'atraso'         => 'Atraso na entrega',
            'qualidade'      => 'Qualidade do trabalho',
            'nao_pagamento'  => 'Não pagamento',
            'outro'          => 'Outro motivo',
        ];

        $reasonLabel = $reasonLabels[$this->dispute->reason] ?? $this->dispute->reason;

        return (new MailMessage)
            ->subject('Disputa aberta no seu projeto')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('**' . $this->openedBy->name . '** abriu uma disputa no projeto abaixo.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Motivo:** ' . $reasonLabel)
            ->action('Ver Disputa', $this->disputeUrl)
            ->line('A nossa equipa de mediação irá acompanhar o caso. Responda com clareza para uma resolução rápida.');
    }
}

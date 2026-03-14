<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Refund;
use App\Models\Service;

class RefundRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Refund $refund,
        public Service $service,
        public string $refundUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'refund_id'  => $this->refund->id,
            'message'    => 'Foi solicitado um reembolso para o projeto "' . $this->service->titulo . '". Aguarda análise.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitação de reembolso recebida')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Recebemos uma solicitação de reembolso para o projeto abaixo.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Motivo:** ' . ($this->refund->reason ?? 'Não especificado'))
            ->action('Ver Solicitação', $this->refundUrl)
            ->line('A nossa equipa irá analisar o pedido e entrará em contacto brevemente.');
    }
}

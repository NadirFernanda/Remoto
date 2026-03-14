<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Service;

class ProposalAcceptedNotification extends Notification implements ShouldQueue
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
            'message'    => 'A sua candidatura para "' . $this->service->titulo . '" foi selecionada! Pode iniciar o projeto.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Candidatura selecionada — o projeto é seu!')
            ->greeting('Excelentes notícias, ' . $notifiable->name . '!')
            ->line('O cliente selecionou a sua candidatura para o projeto abaixo.')
            ->line('**Projeto:** ' . $this->service->titulo)
            ->line('**Valor combinado:** Kz ' . number_format($this->service->valor, 2, ',', '.'))
            ->action('Iniciar Projeto', $this->serviceUrl)
            ->line('O pagamento está em escrow e será libertado após a aprovação da entrega. Bom trabalho!');
    }
}

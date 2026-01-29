<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Service;

class NewProjectNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $service;
    public $serviceUrl;

    public function __construct(Service $service, $serviceUrl)
    {
        $this->service = $service;
        $this->serviceUrl = $serviceUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $descricao = is_array(json_decode($this->service->briefing, true)) && isset(json_decode($this->service->briefing, true)['texto'])
            ? json_decode($this->service->briefing, true)['texto']
            : ($this->service->briefing ?? 'Sem descrição adicional');
        return (new MailMessage)
            ->subject('Novo pedido de serviço disponível')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Um novo pedido de serviço acaba de ser publicado que pode ser do seu interesse.')
            ->line('')
            ->line('**Título:** ' . $this->service->titulo)
            ->line('**Descrição:** ' . $descricao)
            ->line('**Valor:** Kz ' . number_format($this->service->valor, 2, ',', '.'))
            ->action('Acessar Pedido', $this->serviceUrl)
            ->line('Desejamos bons negócios e sucesso no projeto!');
    }
}

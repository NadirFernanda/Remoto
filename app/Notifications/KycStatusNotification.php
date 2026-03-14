<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class KycStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @param string $status  'verified' | 'rejected' */
    public function __construct(
        public string $status,
        public string $kycUrl,
        public ?string $adminNote = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'status'  => $this->status,
            'message' => $this->status === 'verified'
                ? 'A sua identidade foi verificada com sucesso! Pode agora receber pagamentos.'
                : 'A verificação KYC foi rejeitada. ' . ($this->adminNote ? 'Motivo: ' . $this->adminNote : 'Por favor, reenvie os documentos.'),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->status === 'verified') {
            return (new MailMessage)
                ->subject('Identidade verificada com sucesso')
                ->greeting('Parabéns, ' . $notifiable->name . '!')
                ->line('A sua identidade foi verificada com sucesso.')
                ->line('Pode agora receber pagamentos e operar plenamente na plataforma.')
                ->action('Ir para o Dashboard', $this->kycUrl)
                ->line('Obrigado por completar o processo de verificação.');
        }

        return (new MailMessage)
            ->subject('Verificação KYC recusada')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Infelizmente a sua submissão KYC foi recusada.')
            ->when($this->adminNote, fn($mail) => $mail->line('**Motivo:** ' . $this->adminNote))
            ->action('Reenviar Documentos', $this->kycUrl)
            ->line('Certifique-se que os documentos sejam legíveis e válidos antes de reenviar.');
    }
}

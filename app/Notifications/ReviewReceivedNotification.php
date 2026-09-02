<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Review;
use App\Models\User;

class ReviewReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Review $review,
        public User $author,
        public string $profileUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'review_id'  => $this->review->id,
            'service_id' => $this->review->service_id,
            'rating'     => $this->review->rating,
            'message'    => $this->author->name . ' deixou uma avaliação de ' . $this->review->rating . ' estrela(s) para si.',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $ratingText = match ($this->review->rating) {
            5 => '5/5 — excelente',
            4 => '4/5 — muito bom',
            3 => '3/5 — bom',
            2 => '2/5 — aceitável',
            1 => '1/5 — fraco',
            default => $this->review->rating . '/5',
        };

        return (new MailMessage)
            ->subject('Recebeu uma nova avaliação')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('**' . $this->author->name . '** deixou uma avaliação sobre o seu trabalho.')
            ->line('**Classificação:** ' . $ratingText)
            ->line('**Comentário:** ' . ($this->review->comment ?? 'Sem comentário adicional.'))
            ->action('Ver Perfil', $this->profileUrl)
            ->line('Avaliações positivas aumentam a sua visibilidade na plataforma!');
    }
}

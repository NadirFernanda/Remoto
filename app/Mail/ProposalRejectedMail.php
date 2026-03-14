<?php

namespace App\Mail;

use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $freelancer,
        public Service $service,
        public string $mensagem
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Atualização sobre o projeto "' . $this->service->titulo . '"',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.proposal-rejected',
        );
    }
}

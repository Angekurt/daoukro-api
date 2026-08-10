<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationEquipe extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $nomEquipe,
        public readonly string $nomInviteur,
        public readonly string $role,          // manager | editor
        public readonly string $token,
        public readonly string $urlAcceptation,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invitation à rejoindre l'équipe {$this->nomEquipe} — Daoukro Pro",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation-equipe',
        );
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email envoyé au professionnel quand sa fiche est refusée par la mairie.
 */
class FicheRejetee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $nomDestinataire,
        public readonly string $nomFiche,
        public readonly string $typeFiche,
        public readonly ?string $motif = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Votre fiche n'a pas été retenue — {$this->nomFiche}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fiche-rejetee',
        );
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email envoyé au professionnel quand sa fiche est validée et publiée
 * dans l'app Daoukro Digital.
 */
class FicheValidee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $nomDestinataire,
        public readonly string $nomFiche,
        public readonly string $typeFiche,   // Artisan, Hébergement, Bien immobilier, Annonce
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Votre fiche est publiée — {$this->nomFiche}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fiche-validee',
        );
    }
}

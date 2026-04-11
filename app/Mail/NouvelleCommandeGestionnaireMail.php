<?php
// app/Mail/NouvelleCommandeGestionnaireMail.php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouvelleCommandeGestionnaireMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Commande $commande) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: " Nouvelle commande #{$this->commande->id} reçue — ISI BURGER",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.nouvelle-commande-gestionnaire',
            with: ['commande' => $this->commande],
        );
    }
}

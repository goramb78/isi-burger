<?php
// app/Mail/CommandePreteMail.php

namespace App\Mail;

use App\Models\Commande;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommandePreteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Commande $commande)
    {
        // Charger les relations avant la sérialisation
        $this->commande->load(['user', 'items.burger', 'paiement']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🍔 Votre commande #{$this->commande->id} est prête — ISI BURGER",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.commande-prete',
            with: ['commande' => $this->commande],
        );
    }

    // ─── Pièce jointe : facture PDF ──────────────────────────
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.facture', ['commande' => $this->commande])
                  ->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "facture-commande-{$this->commande->id}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}

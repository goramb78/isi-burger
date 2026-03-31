<?php
// app/Http/Controllers/Client/FactureController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    // ─── Télécharger la facture PDF ───────────────────────────
    public function download(Commande $commande)
    {
        // Vérifier appartenance
        abort_if($commande->user_id !== auth()->id(), 403);

        // Seules les commandes prêtes ou payées ont une facture
        abort_unless(
            in_array($commande->statut, ['prete', 'payee']),
            403,
            'La facture n\'est disponible que pour les commandes prêtes ou payées.'
        );

        $commande->load(['user', 'items.burger', 'paiement']);

        $pdf = Pdf::loadView('pdf.facture', compact('commande'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("facture-commande-{$commande->id}.pdf");
    }

    // ─── Aperçu de la facture (gestionnaire) ─────────────────
    public function preview(Commande $commande)
    {
        $commande->load(['user', 'items.burger', 'paiement']);

        $pdf = Pdf::loadView('pdf.facture', compact('commande'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("facture-commande-{$commande->id}.pdf");
    }
}

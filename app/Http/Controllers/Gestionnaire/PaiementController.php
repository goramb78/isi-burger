<?php
// app/Http/Controllers/Gestionnaire/PaiementController.php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    //  Enregistrer un paiement en espèces 
    public function store(Request $request, Commande $commande)
    {
        // Vérifications
        if ($commande->statut !== Commande::STATUT_PRETE) {
            return back()->with('error', 'La commande doit être "Prête" avant d\'être payée.');
        }

        if ($commande->paiement) {
            return back()->with('error', 'Cette commande a déjà été payée.');
        }

        $request->validate([
            'montant' => 'required|numeric|min:0',
        ]);

        // Créer le paiement
        Paiement::create([
            'commande_id'   => $commande->id,
            'montant'       => $request->montant,
            'date_paiement' => now(),
            'mode'          => 'especes',
        ]);

        // Marquer la commande comme payée
        $commande->update(['statut' => Commande::STATUT_PAYEE]);

        return back()->with('success', 'Paiement enregistré avec succès. Commande marquée comme payée.');
    }
}

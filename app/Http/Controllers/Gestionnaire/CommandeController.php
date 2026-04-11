<?php
// app/Http/Controllers/Gestionnaire/CommandeController.php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Mail\CommandePreteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommandeController extends Controller
{
    // Lister toutes les commandes
    public function index(Request $request)
    {
        $query = Commande::with(['user', 'items.burger', 'paiement'])->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $commandes = $query->paginate(15)->withQueryString();
        $statuts   = Commande::statuts();

        return view('gestionnaire.commandes.index', compact('commandes', 'statuts'));
    }

    //  Détails d'une commande 
    public function show(Commande $commande)
    {
        $commande->load(['user', 'items.burger.category', 'paiement']);
        return view('gestionnaire.commandes.show', compact('commande'));
    }

    //  Changer le statut 
    public function updateStatut(Request $request, Commande $commande)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_preparation,prete,payee,annulee',
        ]);

        $ancienStatut = $commande->statut;
        $commande->update(['statut' => $request->statut]);

        // Envoi email + facture PDF quand commande est PRÊTE
        if ($request->statut === Commande::STATUT_PRETE && $ancienStatut !== Commande::STATUT_PRETE) {
            Mail::to($commande->user->email)
                ->queue(new CommandePreteMail($commande));
        }

        return back()->with('success', 'Statut mis à jour : ' . $commande->statut_label);
    }

    //  Annuler une commande 
    public function annuler(Commande $commande)
    {
        if (!$commande->peutEtreAnnulee()) {
            return back()->with('error', 'Cette commande ne peut pas être annulée.');
        }

        // Remettre les stocks
        foreach ($commande->items as $item) {
            $item->burger->increment('stock', $item->quantite);
        }

        $commande->update(['statut' => Commande::STATUT_ANNULEE]);

        return back()->with('success', 'Commande annulée et stocks restaurés.');
    }
}

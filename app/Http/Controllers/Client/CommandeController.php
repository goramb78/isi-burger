<?php
// app/Http/Controllers/Client/CommandeController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Burger;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Mail\CommandeConfirmationMail;
use App\Mail\NouvelleCommandeGestionnaireMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CommandeController extends Controller
{
    // ─── Mes commandes ────────────────────────────────────────
    public function index()
    {
        $commandes = auth()->user()
            ->commandes()
            ->with(['items.burger', 'paiement'])
            ->latest()
            ->paginate(10);

        return view('client.commandes.index', compact('commandes'));
    }

    // ─── Détail d'une commande ────────────────────────────────
    public function show(Commande $commande)
    {
        // S'assurer que la commande appartient au client
        abort_if($commande->user_id !== auth()->id(), 403);

        $commande->load(['items.burger', 'paiement']);
        return view('client.commandes.show', compact('commande'));
    }

    // ─── Formulaire de commande ───────────────────────────────
    public function create(Request $request)
    {
        // Récupérer les burgers sélectionnés
        $burgerIds = $request->get('burgers', []);
        $burgers   = Burger::actif()->whereIn('id', $burgerIds)->get();

        if ($burgers->isEmpty()) {
            return redirect()->route('client.catalogue.index')
                ->with('error', 'Veuillez sélectionner au moins un burger.');
        }

        return view('client.commandes.create', compact('burgers'));
    }

    // ─── Passer la commande ───────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.burger_id' => 'required|exists:burgers,id',
            'items.*.quantite'  => 'required|integer|min:1|max:20',
            'notes'             => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            $lignes = [];

            foreach ($request->items as $item) {
                $burger = Burger::findOrFail($item['burger_id']);

                // Vérifier stock
                if ($burger->archived || $burger->stock < $item['quantite']) {
                    throw new \Exception("Stock insuffisant pour : {$burger->nom}");
                }

                $sousTotal = $burger->prix * $item['quantite'];
                $total    += $sousTotal;

                $lignes[] = [
                    'burger_id'     => $burger->id,
                    'quantite'      => $item['quantite'],
                    'prix_unitaire' => $burger->prix,
                ];

                // Décrémenter le stock
                $burger->decrement('stock', $item['quantite']);
            }

            // Créer la commande
            $commande = Commande::create([
                'user_id' => auth()->id(),
                'statut'  => Commande::STATUT_EN_ATTENTE,
                'total'   => $total,
                'notes'   => $request->notes,
            ]);

            // Créer les lignes
            foreach ($lignes as $ligne) {
                $commande->items()->create($ligne);
            }

            // Emails asynchrones (queue)
            Mail::to(auth()->user()->email)
                ->queue(new CommandeConfirmationMail($commande->load('items.burger')));

            // Notifier le gestionnaire
            $gestionnaires = User::where('role', 'gestionnaire')->get();
            foreach ($gestionnaires as $g) {
                Mail::to($g->email)
                    ->queue(new NouvelleCommandeGestionnaireMail($commande));
            }

            session(['derniere_commande_id' => $commande->id]);
        });

        return redirect()
            ->route('client.commandes.show', session('derniere_commande_id'))
            ->with('success', 'Votre commande a été passée avec succès ! Un email de confirmation vous a été envoyé.');
    }
}

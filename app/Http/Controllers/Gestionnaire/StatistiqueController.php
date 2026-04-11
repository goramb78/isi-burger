<?php
// app/Http/Controllers/Gestionnaire/StatistiqueController.php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Paiement;
use App\Models\Burger;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        //  Stats journalières 
        $commandesEnCours = Commande::whereDate('created_at', $today)
            ->whereIn('statut', ['en_attente', 'en_preparation'])
            ->count();

        $commandesValidees = Commande::whereDate('created_at', $today)
            ->whereIn('statut', ['prete', 'payee'])
            ->count();

        $recettesJournalieres = Paiement::whereDate('date_paiement', $today)
            ->sum('montant');

        //  Commandes par mois (12 derniers mois) 
        $commandesParMois = Commande::select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('YEAR(created_at) as annee'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('annee', 'mois')
            ->orderBy('mois')
            ->get();

        $moisLabels   = [];
        $moisData     = [];
        for ($m = 1; $m <= 12; $m++) {
            $moisLabels[] = \Carbon\Carbon::create()->month($m)->translatedFormat('M');
            $found = $commandesParMois->firstWhere('mois', $m);
            $moisData[] = $found ? $found->total : 0;
        }

        //  Produits par catégorie (mois actuel) 
        $categoriesData = Category::withCount([
            'burgers as commandes_count' => function ($q) {
                $q->join('commande_items', 'burgers.id', '=', 'commande_items.burger_id')
                  ->join('commandes', 'commande_items.commande_id', '=', 'commandes.id')
                  ->whereMonth('commandes.created_at', now()->month)
                  ->whereYear('commandes.created_at', now()->year);
            }
        ])->get();

        $categoriesLabels = $categoriesData->pluck('nom');
        $categoriesValues = $categoriesData->pluck('commandes_count');

        //  Totaux globaux
        $totalCommandes = Commande::count();
        $totalRecettes  = Paiement::sum('montant');
        $totalBurgers   = Burger::where('archived', false)->count();

        return view('gestionnaire.statistiques.index', compact(
            'commandesEnCours',
            'commandesValidees',
            'recettesJournalieres',
            'moisLabels',
            'moisData',
            'categoriesLabels',
            'categoriesValues',
            'totalCommandes',
            'totalRecettes',
            'totalBurgers'
        ));
    }
}

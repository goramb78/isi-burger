<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\CatalogueController;
use App\Http\Controllers\Client\CommandeController as ClientCommandeController;
use App\Http\Controllers\Client\FactureController;
use App\Http\Controllers\Gestionnaire\BurgerController;
use App\Http\Controllers\Gestionnaire\CategoryController;
use App\Http\Controllers\Gestionnaire\CommandeController as GestionnaireCommandeController;
use App\Http\Controllers\Gestionnaire\PaiementController;
use App\Http\Controllers\Gestionnaire\StatistiqueController;

// ─── Page d'accueil ───────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isGestionnaire()
            ? redirect()->route('gestionnaire.statistiques.index')
            : redirect()->route('client.catalogue.index');
    }
    return redirect()->route('login');
});

// ─── Auth (généré par Breeze) ────────────────────────────────────
require __DIR__ . '/auth.php';

// ═══════════════════════════════════════════════════════════════════
//  ESPACE CLIENT
// ═══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'client'])
    ->prefix('catalogue')
    ->name('client.')
    ->group(function () {

        // Catalogue burgers
        Route::get('/', [CatalogueController::class, 'index'])
            ->name('catalogue.index');

        Route::get('/burger/{burger}', [CatalogueController::class, 'show'])
            ->name('catalogue.show');

        // Commandes
        Route::prefix('commandes')->name('commandes.')->group(function () {
            Route::get('/',          [ClientCommandeController::class, 'index'])  ->name('index');
            Route::get('/passer',    [ClientCommandeController::class, 'create']) ->name('create');
            Route::post('/',         [ClientCommandeController::class, 'store'])  ->name('store');
            Route::get('/{commande}',[ClientCommandeController::class, 'show'])   ->name('show');
        });

        // Factures PDF
        Route::get('/facture/{commande}/download', [FactureController::class, 'download'])
            ->name('facture.download');
    });

// ═══════════════════════════════════════════════════════════════════
//  ESPACE GESTIONNAIRE
// ═══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'gestionnaire'])
    ->prefix('gestionnaire')
    ->name('gestionnaire.')
    ->group(function () {

        // ── Statistiques / Dashboard ──
        Route::get('/dashboard', [StatistiqueController::class, 'index'])
            ->name('statistiques.index');

        // ── Catégories ──
        Route::resource('categories', CategoryController::class);

        // ── Burgers ──
        Route::resource('burgers', BurgerController::class);
        Route::patch('/burgers/{burger}/toggle-archive', [BurgerController::class, 'toggleArchive'])
            ->name('burgers.toggle-archive');

        // ── Commandes ──
        Route::prefix('commandes')->name('commandes.')->group(function () {
            Route::get('/',               [GestionnaireCommandeController::class, 'index'])  ->name('index');
            Route::get('/{commande}',     [GestionnaireCommandeController::class, 'show'])   ->name('show');
            Route::patch('/{commande}/statut',  [GestionnaireCommandeController::class, 'updateStatut'])
                ->name('update-statut');
            Route::patch('/{commande}/annuler', [GestionnaireCommandeController::class, 'annuler'])
                ->name('annuler');
        });

        // ── Paiements ──
        Route::post('/paiements/{commande}', [PaiementController::class, 'store'])
            ->name('paiements.store');

        // ── Aperçu facture ──
        Route::get('/facture/{commande}/preview', [FactureController::class, 'preview'])
            ->name('facture.preview');
    });

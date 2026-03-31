<?php
// app/Http/Controllers/Client/CatalogueController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Burger;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    // ─── Catalogue avec filtres ───────────────────────────────
    public function index(Request $request)
    {
        $query = Burger::with('category')->where('archived', false);

        // Filtre par nom/libellé
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtre par prix
        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        // Tri
        $sort = $request->get('sort', 'nom');
        match ($sort) {
            'prix_asc'  => $query->orderBy('prix', 'asc'),
            'prix_desc' => $query->orderBy('prix', 'desc'),
            default     => $query->orderBy('nom', 'asc'),
        };

        $burgers    = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('client.catalogue.index', compact('burgers', 'categories'));
    }

    // ─── Détail d'un burger ───────────────────────────────────
    public function show(Burger $burger)
    {
        abort_if($burger->archived, 404);
        return view('client.catalogue.show', compact('burger'));
    }
}

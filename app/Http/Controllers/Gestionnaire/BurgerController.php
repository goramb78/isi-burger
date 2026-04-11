<?php
// app/Http/Controllers/Gestionnaire/BurgerController.php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Burger;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BurgerController extends Controller
{
    //  Liste tous les burgers (avec archivés) 
    public function index(Request $request)
    {
        $query = Burger::with('category');

        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('statut')) {
            $query->where('archived', $request->statut === 'archive');
        }

        $burgers    = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('gestionnaire.burgers.index', compact('burgers', 'categories'));
    }

    //  Formulaire de création 
    public function create()
    {
        $categories = Category::all();
        return view('gestionnaire.burgers.create', compact('categories'));
    }

    //  Enregistrer un nouveau burger 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'prix'        => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('burgers', 'public');
        }

        Burger::create($validated);

        return redirect()
            ->route('gestionnaire.burgers.index')
            ->with('success', 'Burger créé avec succès.');
    }

    //  Formulaire d'édition 
    public function edit(Burger $burger)
    {
        $categories = Category::all();
        return view('gestionnaire.burgers.edit', compact('burger', 'categories'));
    }

    //  Mettre à jour un burger 
    public function update(Request $request, Burger $burger)
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'prix'        => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($burger->image) {
                Storage::disk('public')->delete($burger->image);
            }
            $validated['image'] = $request->file('image')->store('burgers', 'public');
        }

        $burger->update($validated);

        return redirect()
            ->route('gestionnaire.burgers.index')
            ->with('success', 'Burger mis à jour avec succès.');
    }

    // Archiver / Désarchiver 
    public function toggleArchive(Burger $burger)
    {
        $burger->update(['archived' => !$burger->archived]);

        $msg = $burger->archived ? 'Burger archivé.' : 'Burger remis en ligne.';
        return back()->with('success', $msg);
    }

    //  Supprimer un burger 
    public function destroy(Burger $burger)
    {
        if ($burger->image) {
            Storage::disk('public')->delete($burger->image);
        }

        $burger->delete();

        return redirect()
            ->route('gestionnaire.burgers.index')
            ->with('success', 'Burger supprimé.');
    }
}

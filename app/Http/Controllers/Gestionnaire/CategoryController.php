<?php
// app/Http/Controllers/Gestionnaire/CategoryController.php

namespace App\Http\Controllers\Gestionnaire;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('burgers')->latest()->paginate(15);
        return view('gestionnaire.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('gestionnaire.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:100|unique:categories,nom']);
        Category::create(['nom' => $request->nom]);
        return redirect()->route('gestionnaire.categories.index')
            ->with('success', 'Catégorie créée.');
    }

    public function edit(Category $category)
    {
        return view('gestionnaire.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nom' => 'required|string|max:100|unique:categories,nom,' . $category->id,
        ]);
        $category->update(['nom' => $request->nom]);
        return redirect()->route('gestionnaire.categories.index')
            ->with('success', 'Catégorie modifiée.');
    }

    public function destroy(Category $category)
    {
        if ($category->burgers()->exists()) {
            return back()->with('error', 'Impossible de supprimer une catégorie contenant des burgers.');
        }
        $category->delete();
        return redirect()->route('gestionnaire.categories.index')
            ->with('success', 'Catégorie supprimée.');
    }
}

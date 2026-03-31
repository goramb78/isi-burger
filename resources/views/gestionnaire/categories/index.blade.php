{{-- resources/views/gestionnaire/categories/index.blade.php --}}
@extends('layouts.gestionnaire')

@section('title', 'Catégories')

@section('content')
<div class="row g-3">
    {{-- Formulaire ajout rapide --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle text-danger me-2"></i>Nouvelle catégorie</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('gestionnaire.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom</label>
                        <input type="text" name="nom"
                               class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom') }}"
                               placeholder="Ex: Classiques" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-check-lg"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Liste des catégories --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-tags text-primary me-2"></i>
                    Toutes les catégories ({{ $categories->total() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th class="text-center">Nb burgers</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="fw-semibold">{{ $category->nom }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $category->burgers_count }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('gestionnaire.categories.edit', $category) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('gestionnaire.categories.destroy', $category) }}"
                                          onsubmit="return confirm('Supprimer cette catégorie ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-3 text-muted">
                                Aucune catégorie créée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $categories->links() }}</div>
        </div>
    </div>
</div>
@endsection

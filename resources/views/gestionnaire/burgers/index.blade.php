{{-- resources/views/gestionnaire/burgers/index.blade.php --}}
@extends('layouts.gestionnaire')

@section('title', 'Gestion des Burgers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">{{ $burgers->total() }} produit(s) au total</p>
    </div>
    <a href="{{ route('gestionnaire.burgers.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-circle"></i> Nouveau Burger
    </a>
</div>

{{-- Filtres --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Rechercher un burger…"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actifs</option>
                    <option value="archive" {{ request('statut') === 'archive' ? 'selected' : '' }}>Archivés</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('gestionnaire.burgers.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tableau --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Burger</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($burgers as $burger)
                <tr class="{{ $burger->archived ? 'table-secondary text-muted' : '' }}">
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $burger->image_url }}"
                                 alt="{{ $burger->nom }}"
                                 class="rounded"
                                 style="width:50px;height:50px;object-fit:cover;">
                            <div>
                                <div class="fw-semibold">{{ $burger->nom }}</div>
                                <div class="text-muted small">{{ Str::limit($burger->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $burger->category->nom }}</span>
                    </td>
                    <td class="fw-bold">{{ number_format($burger->prix, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($burger->stock <= 5 && !$burger->archived)
                            <span class="badge bg-danger">{{ $burger->stock }} — Rupture!</span>
                        @elseif($burger->stock <= 15 && !$burger->archived)
                            <span class="badge bg-warning text-dark">{{ $burger->stock }}</span>
                        @else
                            <span class="badge bg-success">{{ $burger->stock }}</span>
                        @endif
                    </td>
                    <td>
                        @if($burger->archived)
                            <span class="badge bg-secondary">Archivé</span>
                        @else
                            <span class="badge bg-success">En ligne</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('gestionnaire.burgers.edit', $burger) }}"
                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('gestionnaire.burgers.toggle-archive', $burger) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm {{ $burger->archived ? 'btn-outline-success' : 'btn-outline-warning' }}"
                                        title="{{ $burger->archived ? 'Remettre en ligne' : 'Archiver' }}">
                                    <i class="bi {{ $burger->archived ? 'bi-eye' : 'bi-archive' }}"></i>
                                </button>
                            </form>
                            <form method="POST"
                                  action="{{ route('gestionnaire.burgers.destroy', $burger) }}"
                                  onsubmit="return confirm('Supprimer définitivement ce burger ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        Aucun burger trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $burgers->links() }}
    </div>
</div>
@endsection

{{-- resources/views/client/catalogue/index.blade.php --}}
@extends('layouts.client')

@section('title', 'Notre Menu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🍔 Notre Menu</h2>
        <p class="text-muted mb-0">{{ $burgers->total() }} burgers disponibles</p>
    </div>
</div>

{{-- ─── Filtres ─── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('client.catalogue.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Recherche</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Nom du burger…"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Catégorie</label>
                <select name="category_id" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Prix min (FCFA)</label>
                <input type="number" name="prix_min" class="form-control"
                       placeholder="0" value="{{ request('prix_min') }}" min="0">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Prix max (FCFA)</label>
                <input type="number" name="prix_max" class="form-control"
                       placeholder="99999" value="{{ request('prix_max') }}" min="0">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Trier par</label>
                <select name="sort" class="form-select">
                    <option value="nom" {{ request('sort') === 'nom' ? 'selected' : '' }}>Nom A→Z</option>
                    <option value="prix_asc" {{ request('sort') === 'prix_asc' ? 'selected' : '' }}>Prix ↑</option>
                    <option value="prix_desc" {{ request('sort') === 'prix_desc' ? 'selected' : '' }}>Prix ↓</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Grille des burgers ─── --}}
@if($burgers->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-search" style="font-size:3rem;"></i>
        <p class="mt-3">Aucun burger trouvé pour ces critères.</p>
        <a href="{{ route('client.catalogue.index') }}" class="btn btn-outline-secondary">
            Réinitialiser les filtres
        </a>
    </div>
@else

{{-- Panier temporaire (formulaire global) --}}
<form method="POST" action="{{ route('client.commandes.store') }}" id="formCommande">
    @csrf
    <div class="row g-3" id="burgerGrid">
        @foreach($burgers as $burger)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card burger-card h-100 shadow-sm">
                {{-- Image --}}
                <div class="position-relative">
                    <img src="{{ $burger->image_url }}"
                         alt="{{ $burger->nom }}"
                         class="card-img-top">
                    <span class="badge bg-secondary position-absolute top-0 start-0 m-2">
                        {{ $burger->category->nom }}
                    </span>
                    @if($burger->isEnRupture())
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-danger">Rupture de stock</span>
                        </div>
                    @endif
                </div>

                <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-bold mb-1">{{ $burger->nom }}</h6>
                    <p class="card-text text-muted small flex-grow-1">
                        {{ Str::limit($burger->description, 80) }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="price-tag">{{ number_format($burger->prix, 0, ',', ' ') }} FCFA</span>
                        @if(!$burger->isEnRupture())
                            <div class="d-flex align-items-center gap-1">
                                <input type="number"
                                       name="items[{{ $burger->id }}][quantite]"
                                       data-burger-id="{{ $burger->id }}"
                                       class="form-control form-control-sm qty-input"
                                       style="width:65px"
                                       min="0" max="{{ $burger->stock }}"
                                       value="0"
                                       placeholder="0">
                                <input type="hidden" name="items[{{ $burger->id }}][burger_id]"
                                       value="{{ $burger->id }}">
                            </div>
                        @else
                            <span class="text-danger small fw-semibold">Indisponible</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Bouton Commander --}}
    <div class="position-fixed bottom-0 start-0 end-0 bg-white border-top shadow-lg p-3 d-flex
                justify-content-between align-items-center" id="commandeBar" style="display:none!important">
        <div>
            <span class="fw-bold fs-5" id="totalAffiche">0 FCFA</span>
            <span class="text-muted ms-2 small" id="qteTotale">0 article(s)</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="resetPanier()">
                <i class="bi bi-trash"></i> Vider
            </button>
            <button type="submit" class="btn btn-primary px-4" id="btnCommander">
                <i class="bi bi-bag-plus"></i> Commander
            </button>
        </div>
    </div>
</form>

{{-- Pagination --}}
<div class="mt-4">
    {{ $burgers->links() }}
</div>
@endif
@endsection

@push('scripts')
<script>
const prices = {
    @foreach($burgers as $b)
        {{ $b->id }}: {{ $b->prix }},
    @endforeach
};

function updateBar() {
    let total = 0, qte = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        const q = parseInt(input.value) || 0;
        if (q > 0) {
            total += q * (prices[input.dataset.burgerId] || 0);
            qte   += q;
        }
    });
    document.getElementById('totalAffiche').textContent = total.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('qteTotale').textContent    = qte + ' article(s)';
    const bar = document.getElementById('commandeBar');
    bar.style.display = qte > 0 ? 'flex' : 'none';
    // Padding body pour éviter que la barre cache du contenu
    document.body.style.paddingBottom = qte > 0 ? '80px' : '0';
}

function resetPanier() {
    document.querySelectorAll('.qty-input').forEach(i => i.value = 0);
    updateBar();
}

document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('input', updateBar);
});
</script>
@endpush

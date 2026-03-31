{{-- resources/views/client/commandes/create.blade.php --}}
@extends('layouts.client')

@section('title', 'Passer une commande')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">🛒 Récapitulatif de votre commande</h2>
    <a href="{{ route('client.catalogue.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Modifier
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0">Vos articles</h6>
            </div>
            <div class="card-body p-0">
                <form method="POST" action="{{ route('client.commandes.store') }}" id="formCommande">
                    @csrf
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Burger</th>
                                <th class="text-center" style="width:120px">Quantité</th>
                                <th class="text-end">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($burgers as $i => $burger)
                            <tr id="row-{{ $burger->id }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $burger->image_url }}"
                                             class="rounded"
                                             style="width:55px;height:55px;object-fit:cover;"
                                             alt="{{ $burger->nom }}">
                                        <div>
                                            <div class="fw-semibold">{{ $burger->nom }}</div>
                                            <div class="text-muted small">
                                                {{ number_format($burger->prix, 0, ',', ' ') }} FCFA / unité
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden"
                                           name="items[{{ $i }}][burger_id]"
                                           value="{{ $burger->id }}">
                                </td>
                                <td>
                                    <input type="number"
                                           name="items[{{ $i }}][quantite]"
                                           class="form-control form-control-sm text-center qty-input"
                                           data-prix="{{ $burger->prix }}"
                                           data-id="{{ $burger->id }}"
                                           value="1"
                                           min="1"
                                           max="{{ $burger->stock }}"
                                           required>
                                </td>
                                <td class="text-end fw-bold" id="subtotal-{{ $burger->id }}">
                                    {{ number_format($burger->prix, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Notes --}}
                    <div class="p-3 border-top">
                        <label class="form-label fw-semibold small">Notes / Instructions spéciales</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Allergies, préférences, instructions…">{{ old('notes') }}</textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Résumé total --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top:80px">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0">Résumé</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Sous-total</span>
                    <span id="totalDisplay" class="fw-bold">0 FCFA</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">TVA (0%)</span>
                    <span>0 FCFA</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fs-5 fw-bold">Total</span>
                    <span class="fs-5 fw-bold text-danger" id="totalFinal">0 FCFA</span>
                </div>
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle"></i>
                    Paiement en espèces au comptoir lors de la récupération.
                </div>
                <button type="submit" form="formCommande" class="btn btn-danger w-100 btn-lg">
                    <i class="bi bi-bag-check"></i> Confirmer la commande
                </button>
                <a href="{{ route('client.catalogue.index') }}"
                   class="btn btn-outline-secondary w-100 mt-2">Annuler</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const formatFCFA = n => n.toLocaleString('fr-FR') + ' FCFA';

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        const qte  = parseInt(input.value) || 0;
        const prix = parseFloat(input.dataset.prix) || 0;
        const sub  = qte * prix;
        total += sub;
        document.getElementById('subtotal-' + input.dataset.id).textContent = formatFCFA(sub);
    });
    document.getElementById('totalDisplay').textContent = formatFCFA(total);
    document.getElementById('totalFinal').textContent   = formatFCFA(total);
}

document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('input', updateTotal);
});

// Calcul initial
updateTotal();
</script>
@endpush

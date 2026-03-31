{{-- resources/views/gestionnaire/commandes/show.blade.php --}}
@extends('layouts.gestionnaire')

@section('title', 'Commande #' . str_pad($commande->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('gestionnaire.commandes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('gestionnaire.facture.preview', $commande) }}"
           class="btn btn-outline-success" target="_blank">
            <i class="bi bi-file-pdf"></i> Aperçu facture
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Infos commande --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    Commande #{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}
                </h6>
                <span class="badge bg-{{ $commande->statut_badge }} fs-6">
                    {{ $commande->statut_label }}
                </span>
            </div>
            <div class="card-body">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Burger</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">Prix unit.</th>
                            <th class="text-end">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commande->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item->burger->image_url }}"
                                         alt="{{ $item->burger->nom }}"
                                         class="rounded"
                                         style="width:40px;height:40px;object-fit:cover;">
                                    <div>
                                        <div class="fw-semibold">{{ $item->burger->nom }}</div>
                                        <div class="text-muted small">{{ $item->burger->category->nom }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ $item->quantite }}</td>
                            <td class="text-end">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end fw-bold">{{ number_format($item->sous_total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold fs-5">Total :</td>
                            <td class="text-end fw-bold fs-5 text-danger">
                                {{ number_format($commande->total, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>

                @if($commande->notes)
                    <div class="alert alert-light border mt-2">
                        <strong><i class="bi bi-chat-left-text"></i> Notes :</strong>
                        {{ $commande->notes }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar actions --}}
    <div class="col-lg-4">
        {{-- Infos client --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-person"></i> Client</h6>
            </div>
            <div class="card-body">
                <div class="fw-semibold">{{ $commande->user->name }}</div>
                <div class="text-muted small">{{ $commande->user->email }}</div>
                <div class="text-muted small mt-1">
                    Passée le {{ $commande->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        {{-- Changer statut --}}
        @if($commande->statut !== 'payee' && $commande->statut !== 'annulee')
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-arrow-repeat"></i> Changer le statut</h6>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ route('gestionnaire.commandes.update-statut', $commande) }}">
                    @csrf @method('PATCH')
                    <select name="statut" class="form-select mb-2">
                        @foreach(\App\Models\Commande::statuts() as $key => $info)
                            <option value="{{ $key }}"
                                {{ $commande->statut === $key ? 'selected' : '' }}>
                                {{ $info['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg"></i> Valider
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Paiement --}}
        @if($commande->statut === 'prete' && !$commande->paiement)
        <div class="card border-0 shadow-sm border-success mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-cash-coin"></i> Enregistrer paiement</h6>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ route('gestionnaire.paiements.store', $commande) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Montant reçu (FCFA)</label>
                        <input type="number" name="montant" class="form-control"
                               value="{{ $commande->total }}" min="0" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Mode</label>
                        <select class="form-select" disabled>
                            <option>Espèces</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle"></i> Confirmer paiement
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Infos paiement enregistré --}}
        @if($commande->paiement)
        <div class="card border-0 shadow-sm border-success mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-check-circle"></i> Payée</h6>
            </div>
            <div class="card-body">
                <div>Montant : <strong>{{ number_format($commande->paiement->montant, 0, ',', ' ') }} FCFA</strong></div>
                <div class="text-muted small">
                    {{ $commande->paiement->date_paiement->format('d/m/Y à H:i') }}
                </div>
                <div>Mode : <span class="badge bg-secondary">Espèces</span></div>
            </div>
        </div>
        @endif

        {{-- Annuler --}}
        @if($commande->peutEtreAnnulee())
        <form method="POST"
              action="{{ route('gestionnaire.commandes.annuler', $commande) }}"
              onsubmit="return confirm('Annuler cette commande ? Les stocks seront restaurés.')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-outline-danger w-100">
                <i class="bi bi-x-circle"></i> Annuler la commande
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

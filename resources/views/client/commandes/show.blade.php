{{-- resources/views/client/commandes/show.blade.php --}}
@extends('layouts.client')

@section('title', 'Commande #' . str_pad($commande->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('client.commandes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Mes commandes
    </a>
    @if(in_array($commande->statut, ['prete','payee']))
        <a href="{{ route('client.facture.download', $commande) }}"
           class="btn btn-success">
            <i class="bi bi-file-pdf"></i> Télécharger la facture PDF
        </a>
    @endif
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">
                    Commande #{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}
                </h5>
                <span class="badge bg-{{ $commande->statut_badge }} fs-6 px-3 py-2">
                    {{ $commande->statut_label }}
                </span>
            </div>
            <div class="card-body">
                <table class="table">
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
                                         class="rounded"
                                         style="width:45px;height:45px;object-fit:cover;"
                                         alt="{{ $item->burger->nom }}">
                                    <span class="fw-semibold">{{ $item->burger->nom }}</span>
                                </div>
                            </td>
                            <td class="text-center">{{ $item->quantite }}</td>
                            <td class="text-end">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end fw-bold">{{ number_format($item->sous_total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end fs-5">Total :</td>
                            <td class="text-end fs-5 text-danger">
                                {{ number_format($commande->total, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Statut et timeline --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">📋 Détails</h6></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Date :</dt>
                    <dd class="col-7">{{ $commande->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-5 text-muted">Statut :</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $commande->statut_badge }}">
                            {{ $commande->statut_label }}
                        </span>
                    </dd>
                    @if($commande->paiement)
                        <dt class="col-5 text-muted">Payé :</dt>
                        <dd class="col-7">
                            {{ number_format($commande->paiement->montant, 0, ',', ' ') }} FCFA<br>
                            <small class="text-muted">{{ $commande->paiement->date_paiement->format('d/m/Y H:i') }}</small>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Timeline statuts --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold">🔄 Suivi</h6></div>
            <div class="card-body">
                @php
                    $steps = [
                        'en_attente'    => ['icon' => 'bi-clock',        'label' => 'En attente'],
                        'en_preparation'=> ['icon' => 'bi-fire',         'label' => 'En préparation'],
                        'prete'         => ['icon' => 'bi-bag-check',    'label' => 'Prête'],
                        'payee'         => ['icon' => 'bi-check-circle', 'label' => 'Payée'],
                    ];
                    $statutsOrdre = ['en_attente','en_preparation','prete','payee'];
                    $statutActuelIndex = array_search($commande->statut, $statutsOrdre);
                @endphp
                @foreach($steps as $key => $step)
                @php
                    $stepIndex = array_search($key, $statutsOrdre);
                    $done    = $statutActuelIndex !== false && $stepIndex <= $statutActuelIndex && $commande->statut !== 'annulee';
                    $current = $commande->statut === $key;
                @endphp
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:36px;height:36px;background:{{ $done ? ($current ? '#e63946' : '#198754') : '#dee2e6' }}">
                        <i class="bi {{ $step['icon'] }} text-white"></i>
                    </div>
                    <div>
                        <div class="fw-semibold {{ $done ? 'text-dark' : 'text-muted' }}">
                            {{ $step['label'] }}
                        </div>
                        @if($current)
                            <small class="text-muted">Statut actuel</small>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($commande->statut === 'annulee')
                <div class="alert alert-danger mt-2 py-2 mb-0">
                    <i class="bi bi-x-circle"></i> Commande annulée
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

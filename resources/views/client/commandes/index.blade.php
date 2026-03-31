{{-- resources/views/client/commandes/index.blade.php --}}
@extends('layouts.client')

@section('title', 'Mes Commandes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">📋 Mes Commandes</h2>
        <p class="text-muted mb-0">Suivi de toutes vos commandes</p>
    </div>
    <a href="{{ route('client.catalogue.index') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouvelle commande
    </a>
</div>

@if($commandes->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-bag-x" style="font-size:4rem;color:#dee2e6;"></i>
        <h5 class="mt-3 text-muted">Vous n'avez pas encore de commandes</h5>
        <a href="{{ route('client.catalogue.index') }}" class="btn btn-primary mt-2">
            Voir le menu
        </a>
    </div>
@else
    <div class="row g-3">
        @foreach($commandes as $commande)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="text-muted small">N° Commande</div>
                            <div class="fw-bold fs-5">#{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Articles</div>
                            <div>
                                @foreach($commande->items->take(2) as $item)
                                    <span class="badge bg-light text-dark border me-1">
                                        {{ $item->burger->nom }} ×{{ $item->quantite }}
                                    </span>
                                @endforeach
                                @if($commande->items->count() > 2)
                                    <span class="text-muted small">+{{ $commande->items->count() - 2 }} autres</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="badge bg-{{ $commande->statut_badge }} fs-6 px-3 py-2">
                                {{ $commande->statut_label }}
                            </span>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="text-muted small">Total</div>
                            <div class="fw-bold text-danger">
                                {{ number_format($commande->total, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-muted small">Date</div>
                            <div class="small">{{ $commande->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-1 text-end">
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('client.commandes.show', $commande) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($commande->statut, ['prete','payee']))
                                    <a href="{{ route('client.facture.download', $commande) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $commandes->links() }}</div>
@endif
@endsection

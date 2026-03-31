{{-- resources/views/gestionnaire/commandes/index.blade.php --}}
@extends('layouts.gestionnaire')

@section('title', 'Gestion des Commandes')

@section('content')

{{-- Filtres --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control"
                       placeholder="Client (nom ou email)…"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    @foreach($statuts as $key => $info)
                        <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                            {{ $info['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date" class="form-control"
                       value="{{ request('date') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
            </div>
            <div class="col-auto">
                <a href="{{ route('gestionnaire.commandes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Compteurs par statut --}}
<div class="row g-2 mb-4">
    @foreach($statuts as $key => $info)
    <div class="col-6 col-md-auto">
        <a href="{{ route('gestionnaire.commandes.index', ['statut' => $key]) }}"
           class="text-decoration-none">
            <span class="badge bg-{{ $info['badge'] }} px-3 py-2 fs-6">
                {{ $info['label'] }} :
                {{ \App\Models\Commande::where('statut', $key)->count() }}
            </span>
        </a>
    </div>
    @endforeach
</div>

{{-- Tableau --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Articles</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commandes as $commande)
                <tr>
                    <td class="fw-bold text-muted">#{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="fw-semibold">{{ $commande->user->name }}</div>
                        <div class="text-muted small">{{ $commande->user->email }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $commande->items->sum('quantite') }} article(s)
                        </span>
                    </td>
                    <td class="fw-bold text-danger">
                        {{ number_format($commande->total, 0, ',', ' ') }} FCFA
                    </td>
                    <td>
                        <span class="badge bg-{{ $commande->statut_badge }}">
                            {{ $commande->statut_label }}
                        </span>
                    </td>
                    <td class="text-muted small">
                        {{ $commande->created_at->format('d/m/Y') }}<br>
                        {{ $commande->created_at->format('H:i') }}
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('gestionnaire.commandes.show', $commande) }}"
                               class="btn btn-sm btn-outline-primary" title="Voir détails">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($commande->peutEtreAnnulee())
                            <form method="POST"
                                  action="{{ route('gestionnaire.commandes.annuler', $commande) }}"
                                  onsubmit="return confirm('Annuler cette commande ?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Aucune commande trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $commandes->links() }}
    </div>
</div>
@endsection

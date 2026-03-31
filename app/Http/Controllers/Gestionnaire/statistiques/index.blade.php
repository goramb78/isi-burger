{{-- resources/views/gestionnaire/statistiques/index.blade.php --}}
@extends('layouts.gestionnaire')

@section('title', 'Tableau de bord')

@section('content')

{{-- ─── KPI Cards ─── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card card bg-warning bg-opacity-10 border-warning border-opacity-25">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Commandes en cours</div>
                    <div class="fs-2 fw-bold text-warning">{{ $commandesEnCours }}</div>
                    <div class="text-muted small">Aujourd'hui</div>
                </div>
                <div class="fs-2 text-warning opacity-50"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card card bg-success bg-opacity-10 border-success border-opacity-25">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Commandes validées</div>
                    <div class="fs-2 fw-bold text-success">{{ $commandesValidees }}</div>
                    <div class="text-muted small">Aujourd'hui</div>
                </div>
                <div class="fs-2 text-success opacity-50"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card card bg-danger bg-opacity-10 border-danger border-opacity-25">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Recettes journalières</div>
                    <div class="fs-2 fw-bold text-danger">
                        {{ number_format($recettesJournalieres, 0, ',', ' ') }}
                    </div>
                    <div class="text-muted small">FCFA aujourd'hui</div>
                </div>
                <div class="fs-2 text-danger opacity-50"><i class="bi bi-cash-coin"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card card bg-primary bg-opacity-10 border-primary border-opacity-25">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small mb-1">Total recettes</div>
                    <div class="fs-2 fw-bold text-primary">
                        {{ number_format($totalRecettes, 0, ',', ' ') }}
                    </div>
                    <div class="text-muted small">FCFA cumulés</div>
                </div>
                <div class="fs-2 text-primary opacity-50"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Graphiques ─── --}}
<div class="row g-3 mb-4">
    {{-- Commandes par mois --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-bar-chart-line text-primary me-2"></i>
                    Commandes par mois — {{ now()->year }}
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartCommandes" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- Produits par catégorie --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-pie-chart text-danger me-2"></i>
                    Ventes par catégorie — {{ now()->translatedFormat('F Y') }}
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartCategories" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ─── Dernières commandes ─── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-clock-history text-warning me-2"></i>Dernières commandes
        </h6>
        <a href="{{ route('gestionnaire.commandes.index') }}" class="btn btn-sm btn-outline-primary">
            Voir tout
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Client</th><th>Statut</th><th>Total</th><th>Date</th><th></th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Commande::with('user')->latest()->take(8)->get() as $c)
                <tr>
                    <td class="fw-bold">#{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $c->user->name }}</td>
                    <td>
                        <span class="badge bg-{{ $c->statut_badge }}">{{ $c->statut_label }}</span>
                    </td>
                    <td class="fw-bold">{{ number_format($c->total, 0, ',', ' ') }} FCFA</td>
                    <td class="text-muted small">{{ $c->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('gestionnaire.commandes.show', $c) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Graphique Commandes par Mois ──────────────────────────────────
const ctxMois = document.getElementById('chartCommandes').getContext('2d');
new Chart(ctxMois, {
    type: 'bar',
    data: {
        labels: {!! json_encode($moisLabels) !!},
        datasets: [{
            label: 'Commandes',
            data: {!! json_encode($moisData) !!},
            backgroundColor: 'rgba(230, 57, 70, 0.2)',
            borderColor: 'rgba(230, 57, 70, 1)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// ── Graphique Catégories ──────────────────────────────────────────
const ctxCat = document.getElementById('chartCategories').getContext('2d');
new Chart(ctxCat, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoriesLabels) !!},
        datasets: [{
            data: {!! json_encode($categoriesValues) !!},
            backgroundColor: [
                '#e63946','#457b9d','#2a9d8f','#e9c46a',
                '#f4a261','#264653','#a8dadc'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endpush

{{-- resources/views/layouts/client.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Menu') — ISI BURGER</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root { --primary: #e63946; --primary-hover: #c1121f; }
        body { background: #f8f9fa; }
        .navbar-brand { font-weight: 800; color: var(--primary) !important; font-size: 1.4rem; }
        .navbar { background: #1d1d1d !important; }
        .nav-link { color: #adb5bd !important; }
        .nav-link:hover, .nav-link.active { color: #fff !important; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        .burger-card { border: none; border-radius: 14px; overflow: hidden;
            transition: transform .2s, box-shadow .2s; }
        .burger-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,.12); }
        .burger-card img { height: 200px; object-fit: cover; width: 100%; }
        .badge-stock-rupture { background: #e63946; }
        .price-tag { font-size: 1.15rem; font-weight: 700; color: var(--primary); }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══ NAVBAR ═══ --}}
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-lg">
        <a class="navbar-brand" href="{{ route('client.catalogue.index') }}">🍔 ISI BURGER</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.catalogue*') ? 'active' : '' }}"
                       href="{{ route('client.catalogue.index') }}">
                        <i class="bi bi-grid-3x3-gap"></i> Menu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.commandes*') ? 'active' : '' }}"
                       href="{{ route('client.commandes.index') }}">
                        <i class="bi bi-bag-check"></i> Mes commandes
                        @php
                            $mesCommandes = auth()->user()->commandes()
                                ->whereIn('statut', ['en_attente','en_preparation'])->count();
                        @endphp
                        @if($mesCommandes > 0)
                            <span class="badge bg-warning text-dark">{{ $mesCommandes }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- ═══ CONTENU ═══ --}}
<main class="container-lg py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<footer class="text-center py-3 text-muted" style="font-size:.85rem;">
    &copy; {{ date('Y') }} ISI BURGER — Tous droits réservés
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

{{-- resources/views/layouts/gestionnaire.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestionnaire') — ISI BURGER</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #e63946;
            --dark-bg: #1d1d1d;
        }
        body { background: #f4f5f7; }

        /* ─── Sidebar ───────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--dark-bg);
            display: flex; flex-direction: column;
            overflow-y: auto; z-index: 100;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.4rem; font-weight: 800;
            color: var(--primary);
            border-bottom: 1px solid #333;
            text-decoration: none;
        }
        .sidebar-brand span { color: #fff; font-weight: 300; }
        .sidebar .nav-link {
            color: #adb5bd; padding: .65rem 1.25rem;
            border-radius: 8px; margin: 2px 8px;
            transition: background .2s, color .2s;
            font-size: .95rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(230,57,70,.15);
            color: #fff;
        }
        .sidebar .nav-link i { width: 22px; }
        .sidebar-section {
            padding: .4rem 1.25rem .2rem;
            font-size: .7rem; color: #555;
            text-transform: uppercase; letter-spacing: .08em;
            margin-top: .75rem;
        }

        /* ─── Main content ──────────── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: .75rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 99;
        }
        .page-content { padding: 2rem 1.5rem; }

        /* ─── Cards ─────────────────── */
        .stat-card {
            border: none; border-radius: 12px;
            padding: 1.25rem;
            transition: transform .2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar">
    <a class="sidebar-brand" href="{{ route('gestionnaire.statistiques.index') }}">
        🍔 ISI<span>BURGER</span>
    </a>

    <nav class="flex-grow-1 py-2">
        <div class="sidebar-section">Tableau de bord</div>
        <a class="nav-link {{ request()->routeIs('gestionnaire.statistiques*') ? 'active' : '' }}"
           href="{{ route('gestionnaire.statistiques.index') }}">
            <i class="bi bi-speedometer2"></i> Statistiques
        </a>

        <div class="sidebar-section">Produits</div>
        <a class="nav-link {{ request()->routeIs('gestionnaire.burgers*') ? 'active' : '' }}"
           href="{{ route('gestionnaire.burgers.index') }}">
            <i class="bi bi-egg-fried"></i> Burgers
        </a>
        <a class="nav-link {{ request()->routeIs('gestionnaire.categories*') ? 'active' : '' }}"
           href="{{ route('gestionnaire.categories.index') }}">
            <i class="bi bi-tags"></i> Catégories
        </a>

        <div class="sidebar-section">Commandes</div>
        <a class="nav-link {{ request()->routeIs('gestionnaire.commandes*') ? 'active' : '' }}"
           href="{{ route('gestionnaire.commandes.index') }}">
            <i class="bi bi-bag-check"></i> Commandes
            @php
                $nbEnAttente = \App\Models\Commande::where('statut','en_attente')->count();
            @endphp
            @if($nbEnAttente > 0)
                <span class="badge bg-danger ms-1">{{ $nbEnAttente }}</span>
            @endif
        </a>
    </nav>

    <div class="border-top border-secondary p-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                 style="width:36px;height:36px">
                <i class="bi bi-person-fill text-white"></i>
            </div>
            <div>
                <div class="text-white fw-semibold" style="font-size:.85rem">{{ auth()->user()->name }}</div>
                <div class="text-muted" style="font-size:.75rem">Gestionnaire</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- ═══ MAIN ═══ --}}
<div class="main-wrapper">
    <div class="topbar">
        <h5 class="mb-0 fw-bold">@yield('title', 'Tableau de bord')</h5>
        <div class="text-muted" style="font-size:.88rem">
            <i class="bi bi-calendar3"></i> {{ now()->translatedFormat('l d F Y') }}
        </div>
    </div>

    <div class="page-content">
        {{-- Alertes flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

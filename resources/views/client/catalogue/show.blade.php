{{-- resources/views/client/catalogue/show.blade.php --}}
@extends('layouts.client')

@section('title', $burger->nom)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('client.catalogue.index') }}">Menu</a>
        </li>
        <li class="breadcrumb-item active">{{ $burger->nom }}</li>
    </ol>
</nav>

<div class="row g-4 align-items-start">
    {{-- Image --}}
    <div class="col-md-5">
        <div class="position-relative">
            <img src="{{ $burger->image_url }}"
                 alt="{{ $burger->nom }}"
                 class="img-fluid rounded-3 shadow"
                 style="width:100%;height:360px;object-fit:cover;">
            @if($burger->isEnRupture())
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center
                            justify-content-center rounded-3"
                     style="background:rgba(0,0,0,.45);">
                    <span class="badge bg-danger fs-5 px-4 py-2">Rupture de stock</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Infos + commander --}}
    <div class="col-md-7">
        <span class="badge bg-secondary mb-2">{{ $burger->category->nom }}</span>
        <h1 class="fw-bold mb-1">{{ $burger->nom }}</h1>
        <div class="fs-3 fw-bold text-danger mb-3">
            {{ number_format($burger->prix, 0, ',', ' ') }} FCFA
        </div>

        @if($burger->description)
            <p class="text-muted mb-4" style="line-height:1.7;">{{ $burger->description }}</p>
        @endif

        <div class="mb-3">
            @if($burger->isEnRupture())
                <div class="alert alert-danger d-inline-block py-2 px-3">
                    <i class="bi bi-x-circle"></i> Ce burger est actuellement indisponible.
                </div>
            @else
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                    <i class="bi bi-check-circle"></i> En stock ({{ $burger->stock }} restants)
                </span>
            @endif
        </div>

        @if(!$burger->isEnRupture())
        <form method="POST" action="{{ route('client.commandes.store') }}" class="d-flex gap-3 align-items-end">
            @csrf
            <input type="hidden" name="items[0][burger_id]" value="{{ $burger->id }}">
            <div>
                <label class="form-label fw-semibold small">Quantité</label>
                <input type="number" name="items[0][quantite]"
                       class="form-control" style="width:90px"
                       value="1" min="1" max="{{ $burger->stock }}" required>
            </div>
            <button type="submit" class="btn btn-danger btn-lg px-4">
                <i class="bi bi-bag-plus"></i> Commander
            </button>
        </form>
        @endif
    </div>
</div>

<div class="mt-5">
    <a href="{{ route('client.catalogue.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour au menu
    </a>
</div>
@endsection

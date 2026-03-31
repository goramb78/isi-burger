{{-- resources/views/gestionnaire/burgers/edit.blade.php --}}
@extends('layouts.gestionnaire')

@section('title', 'Modifier : ' . $burger->nom)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('gestionnaire.burgers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <form method="POST"
          action="{{ route('gestionnaire.burgers.toggle-archive', $burger) }}">
        @csrf @method('PATCH')
        <button type="submit"
                class="btn {{ $burger->archived ? 'btn-outline-success' : 'btn-outline-warning' }}">
            <i class="bi {{ $burger->archived ? 'bi-eye' : 'bi-archive' }}"></i>
            {{ $burger->archived ? 'Remettre en ligne' : 'Archiver' }}
        </button>
    </form>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-3">
                <img src="{{ $burger->image_url }}" class="rounded"
                     style="width:45px;height:45px;object-fit:cover;">
                <div>
                    <h6 class="fw-bold mb-0">Modifier : {{ $burger->nom }}</h6>
                    <small class="text-muted">{{ $burger->category->nom }}</small>
                </div>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ route('gestionnaire.burgers.update', $burger) }}"
                      enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $burger->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Prix (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" name="prix"
                                   class="form-control @error('prix') is-invalid @enderror"
                                   value="{{ old('prix', $burger->prix) }}" min="0" step="50" required>
                            @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', $burger->stock) }}" min="0" required>
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
                            <select name="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $burger->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nouvelle image</label>
                            <input type="file" name="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/webp"
                                   onchange="previewImage(this)">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="mt-2 d-flex gap-2 align-items-center">
                                <img id="imgPreview" src="{{ $burger->image_url }}"
                                     alt="Aperçu" class="rounded"
                                     style="height:80px;object-fit:cover;">
                                <small class="text-muted">Image actuelle</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $burger->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('gestionnaire.burgers.index') }}"
                               class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-check-lg"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

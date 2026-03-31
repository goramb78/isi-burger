{{-- resources/views/gestionnaire/burgers/create.blade.php --}}
@extends('layouts.gestionnaire')

@section('title', 'Nouveau Burger')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('gestionnaire.burgers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle text-danger me-2"></i>Ajouter un burger</h6>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ route('gestionnaire.burgers.store') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        {{-- Nom --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom du burger <span class="text-danger">*</span></label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom') }}" placeholder="Ex: ISI Classic" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Prix --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Prix (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" name="prix"
                                   class="form-control @error('prix') is-invalid @enderror"
                                   value="{{ old('prix') }}" min="0" step="50" required>
                            @error('prix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Stock --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', 0) }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Catégorie --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
                            <select name="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Choisir --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Image</label>
                            <input type="file" name="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/webp"
                                   onchange="previewImage(this)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <img id="imgPreview" src="#" alt="Aperçu"
                                 class="mt-2 rounded d-none"
                                 style="height:100px;object-fit:cover;">
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Décrivez les ingrédients et le goût…">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('gestionnaire.burgers.index') }}"
                               class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-check-lg"></i> Enregistrer
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
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

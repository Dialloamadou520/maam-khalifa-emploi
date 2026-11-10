@extends('layouts.espace')

@section('titre', $prestation->exists ? 'Modifier la prestation' : 'Nouvelle prestation')

@section('espace')
    <form method="POST" action="{{ $prestation->exists ? route('admin.prestations.update', $prestation) : route('admin.prestations.store') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        @if ($prestation->exists) @method('PUT') @endif
        <div class="card-body p-4 row g-3">
            <div class="col-md-8">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="nom" name="nom" value="{{ old('nom', $prestation->nom) }}" class="form-control @error('nom') is-invalid @enderror" required>
                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="cible" class="form-label">Destinée aux</label>
                <select id="cible" name="cible" class="form-select">
                    @foreach (\App\Enums\ServiceTarget::options() as $v => $l)
                        <option value="{{ $v }}" @selected(old('cible', $prestation->cible?->value) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="prix" class="form-label">Tarif (FCFA)</label>
                <input type="number" min="0" step="500" id="prix" name="prix" value="{{ old('prix', $prestation->prix) }}" class="form-control @error('prix') is-invalid @enderror" required>
                @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="delai" class="form-label">Délai indicatif</label>
                <input type="text" id="delai" name="delai" value="{{ old('delai', $prestation->delai) }}" class="form-control" placeholder="72 h, 1 semaine…">
            </div>
            <div class="col-md-2">
                <label for="ordre" class="form-label">Ordre</label>
                <input type="number" min="0" id="ordre" name="ordre" value="{{ old('ordre', $prestation->ordre) }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="est_active" name="est_active" value="1" @checked(old('est_active', $prestation->est_active))>
                    <label class="form-check-label" for="est_active">Active</label>
                </div>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $prestation->description) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('admin.prestations.index') }}" class="btn btn-link">Annuler</a>
            </div>
        </div>
    </form>
@endsection

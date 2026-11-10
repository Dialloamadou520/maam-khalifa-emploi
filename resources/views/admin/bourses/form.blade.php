@extends('layouts.espace')

@section('titre', $bourse->exists ? 'Modifier la bourse' : 'Nouvelle bourse ou financement')

@section('espace')
    <form method="POST" action="{{ $bourse->exists ? route('admin.bourses.update', $bourse) : route('admin.bourses.store') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        @if ($bourse->exists) @method('PUT') @endif
        <div class="card-body p-4 row g-3">
            @foreach ([['titre', 'Titre', 'col-md-8', true], ['organisme', 'Organisme', 'col-md-4', true], ['pays', 'Pays', 'col-md-4', false], ['niveau', 'Niveau', 'col-md-4', false], ['domaine', 'Domaine', 'col-md-4', false], ['montant', 'Montant / prise en charge', 'col-md-6', false], ['lien_externe', 'Lien officiel', 'col-md-6', false]] as [$champ, $libelle, $col, $requis])
                <div class="{{ $col }}">
                    <label for="{{ $champ }}" class="form-label">{{ $libelle }}</label>
                    <input type="{{ $champ === 'lien_externe' ? 'url' : 'text' }}" id="{{ $champ }}" name="{{ $champ }}" value="{{ old($champ, $bourse->{$champ}) }}" class="form-control @error($champ) is-invalid @enderror" @required($requis)>
                    @error($champ)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endforeach
            <div class="col-md-4">
                <label for="type" class="form-label">Type</label>
                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                    @foreach (\App\Enums\ScholarshipType::options() as $v => $l)
                        <option value="{{ $v }}" @selected(old('type', $bourse->type?->value) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="date_limite" class="form-label">Date limite</label>
                <input type="date" id="date_limite" name="date_limite" value="{{ old('date_limite', $bourse->date_limite?->format('Y-m-d')) }}" class="form-control @error('date_limite') is-invalid @enderror">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="est_publiee" name="est_publiee" value="1" @checked(old('est_publiee', $bourse->est_publiee))>
                    <label class="form-check-label" for="est_publiee">Publiée</label>
                </div>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $bourse->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="conditions" class="form-label">Conditions d'éligibilité</label>
                <textarea id="conditions" name="conditions" rows="3" class="form-control">{{ old('conditions', $bourse->conditions) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('admin.bourses.index') }}" class="btn btn-link">Annuler</a>
            </div>
        </div>
    </form>
@endsection

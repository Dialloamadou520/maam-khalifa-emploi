@extends('layouts.espace')

@section('titre', $offre->exists ? 'Modifier l\'offre' : 'Nouvelle offre d\'emploi')
@section('sous_titre', $offre->exists ? $offre->titre : 'Décrivez le poste : l\'agence la vérifie puis la publie.')

@section('espace')
    @if ($offre->exists && $offre->statut === \App\Enums\OfferStatus::Publiee)
        <div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i> Toute modification repasse l'offre en validation : elle sera masquée jusqu'à la nouvelle validation de l'agence.</div>
    @endif
    <form method="POST" action="{{ $offre->exists ? route('recruteur.offres.update', $offre) : route('recruteur.offres.store') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        @if ($offre->exists) @method('PUT') @endif
        <div class="card-body p-4 row g-3">
            <div class="col-md-8">
                <label for="titre" class="form-label">Intitulé du poste</label>
                <input type="text" id="titre" name="titre" value="{{ old('titre', $offre->titre) }}" class="form-control @error('titre') is-invalid @enderror" required>
                @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="type_contrat" class="form-label">Type de contrat</label>
                <select id="type_contrat" name="type_contrat" class="form-select @error('type_contrat') is-invalid @enderror" required>
                    <option value="">—</option>
                    @foreach (\App\Enums\ContractType::options() as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(old('type_contrat', $offre->type_contrat?->value) === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </select>
                @error('type_contrat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="lieu" class="form-label">Lieu</label>
                <input type="text" id="lieu" name="lieu" value="{{ old('lieu', $offre->lieu) }}" class="form-control @error('lieu') is-invalid @enderror" required>
                @error('lieu')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="secteur" class="form-label">Secteur</label>
                <input type="text" id="secteur" name="secteur" value="{{ old('secteur', $offre->secteur) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="nombre_postes" class="form-label">Nombre de postes</label>
                <input type="number" min="1" id="nombre_postes" name="nombre_postes" value="{{ old('nombre_postes', $offre->nombre_postes) }}" class="form-control @error('nombre_postes') is-invalid @enderror" required>
                @error('nombre_postes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="niveau_etude" class="form-label">Niveau d'études</label>
                <input type="text" id="niveau_etude" name="niveau_etude" value="{{ old('niveau_etude', $offre->niveau_etude) }}" class="form-control" placeholder="Licence, Master…">
            </div>
            <div class="col-md-4">
                <label for="experience_min" class="form-label">Expérience minimale (années)</label>
                <input type="number" min="0" id="experience_min" name="experience_min" value="{{ old('experience_min', $offre->experience_min) }}" class="form-control @error('experience_min') is-invalid @enderror">
                @error('experience_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="date_limite" class="form-label">Date limite de candidature</label>
                <input type="date" id="date_limite" name="date_limite" value="{{ old('date_limite', $offre->date_limite?->format('Y-m-d')) }}" class="form-control @error('date_limite') is-invalid @enderror">
                @error('date_limite')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="salaire_min" class="form-label">Salaire mensuel minimum (FCFA)</label>
                <input type="number" min="0" step="5000" id="salaire_min" name="salaire_min" value="{{ old('salaire_min', $offre->salaire_min) }}" class="form-control @error('salaire_min') is-invalid @enderror">
                @error('salaire_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="salaire_max" class="form-label">Salaire mensuel maximum (FCFA)</label>
                <input type="number" min="0" step="5000" id="salaire_max" name="salaire_max" value="{{ old('salaire_max', $offre->salaire_max) }}" class="form-control @error('salaire_max') is-invalid @enderror">
                @error('salaire_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description du poste</label>
                <textarea id="description" name="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $offre->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="profil_recherche" class="form-label">Profil recherché</label>
                <textarea id="profil_recherche" name="profil_recherche" rows="4" class="form-control">{{ old('profil_recherche', $offre->profil_recherche) }}</textarea>
            </div>
            <div class="col-md-6">
                <label for="avantages" class="form-label">Avantages</label>
                <textarea id="avantages" name="avantages" rows="4" class="form-control">{{ old('avantages', $offre->avantages) }}</textarea>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Soumettre à validation</button>
                @if (! $offre->exists || $offre->statut === \App\Enums\OfferStatus::Brouillon)
                    <button type="submit" name="brouillon" value="1" class="btn btn-outline-secondary">Enregistrer en brouillon</button>
                @endif
                <a href="{{ $offre->exists ? route('recruteur.offres.show', $offre) : route('recruteur.offres.index') }}" class="btn btn-link">Annuler</a>
            </div>
        </div>
    </form>
@endsection

@extends('layouts.espace')

@section('titre', 'Mon entreprise')
@section('sous_titre', 'Ces informations apparaissent sur vos offres d\'emploi.')

@section('espace')
    @unless ($entreprise->est_verifiee)
        <div class="alert alert-warning small"><i class="bi bi-hourglass-split me-1"></i> Compte en cours de vérification par l'agence.</div>
    @endunless
    <form method="POST" action="{{ route('recruteur.entreprise.update') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        @method('PUT')
        <div class="card-body p-4 row g-3">
            <div class="col-md-8">
                <label for="nom" class="form-label">Raison sociale</label>
                <input type="text" id="nom" name="nom" value="{{ old('nom', $entreprise->nom) }}" class="form-control @error('nom') is-invalid @enderror" required>
                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="ninea" class="form-label">NINEA</label>
                <input type="text" id="ninea" name="ninea" value="{{ old('ninea', $entreprise->ninea) }}" class="form-control">
            </div>
            <div class="col-md-8">
                <label for="secteur" class="form-label">Secteur d'activité</label>
                <input type="text" id="secteur" name="secteur" value="{{ old('secteur', $entreprise->secteur) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="taille" class="form-label">Effectif</label>
                <select id="taille" name="taille" class="form-select @error('taille') is-invalid @enderror">
                    <option value="">—</option>
                    @foreach ($tailles as $taille)
                        <option value="{{ $taille }}" @selected(old('taille', $entreprise->taille) === $taille)>{{ $taille }} salariés</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', $entreprise->telephone) }}" class="form-control @error('telephone') is-invalid @enderror" required>
                @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="email_contact" class="form-label">Email de contact</label>
                <input type="email" id="email_contact" name="email_contact" value="{{ old('email_contact', $entreprise->email_contact) }}" class="form-control @error('email_contact') is-invalid @enderror">
                @error('email_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" id="adresse" name="adresse" value="{{ old('adresse', $entreprise->adresse) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="ville" class="form-label">Ville</label>
                <input type="text" id="ville" name="ville" value="{{ old('ville', $entreprise->ville) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="pays" class="form-label">Pays</label>
                <input type="text" id="pays" name="pays" value="{{ old('pays', $entreprise->pays) }}" class="form-control">
            </div>
            <div class="col-12">
                <label for="site_web" class="form-label">Site web</label>
                <input type="url" id="site_web" name="site_web" value="{{ old('site_web', $entreprise->site_web) }}" class="form-control @error('site_web') is-invalid @enderror" placeholder="https://">
                @error('site_web')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Présentation de l'entreprise</label>
                <textarea id="description" name="description" rows="5" class="form-control">{{ old('description', $entreprise->description) }}</textarea>
            </div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Enregistrer</button></div>
        </div>
    </form>
@endsection

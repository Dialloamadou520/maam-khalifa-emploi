@extends('layouts.espace')

@section('titre', 'Mon profil professionnel')
@section('sous_titre', 'Ces informations sont visibles par l\'agence et, si vous l\'acceptez, par les recruteurs.')

@section('espace')
    <form method="POST" action="{{ route('candidat.profil.update') }}" class="card border-0 shadow-sm mb-4" novalidate>
        @csrf
        @method('PUT')
        <div class="card-body p-4">
            <h2 class="h6 fw-bold mb-3">Identité &amp; contact</h2>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nom complet</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="form-control @error('telephone') is-invalid @enderror" required>
                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="date_naissance" class="form-label">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $profil->date_naissance?->format('Y-m-d')) }}" class="form-control @error('date_naissance') is-invalid @enderror">
                    @error('date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="genre" class="form-label">Genre</label>
                    <select id="genre" name="genre" class="form-select">
                        <option value="">—</option>
                        <option value="femme" @selected(old('genre', $profil->genre) === 'femme')>Femme</option>
                        <option value="homme" @selected(old('genre', $profil->genre) === 'homme')>Homme</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ville" class="form-label">Ville</label>
                    <input type="text" id="ville" name="ville" value="{{ old('ville', $profil->ville) }}" class="form-control">
                </div>
                <div class="col-md-8">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="{{ old('adresse', $profil->adresse) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="pays" class="form-label">Pays</label>
                    <input type="text" id="pays" name="pays" value="{{ old('pays', $profil->pays) }}" class="form-control">
                </div>
            </div>

            <h2 class="h6 fw-bold mb-3">Parcours</h2>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="titre_profil" class="form-label">Titre du profil</label>
                    <input type="text" id="titre_profil" name="titre_profil" value="{{ old('titre_profil', $profil->titre_profil) }}" class="form-control" placeholder="Ex. Comptable confirmé">
                </div>
                <div class="col-md-6">
                    <label for="domaine" class="form-label">Domaine</label>
                    <input type="text" id="domaine" name="domaine" value="{{ old('domaine', $profil->domaine) }}" class="form-control" placeholder="Finance, informatique…">
                </div>
                <div class="col-md-4">
                    <label for="niveau_etude" class="form-label">Niveau d'études</label>
                    <select id="niveau_etude" name="niveau_etude" class="form-select">
                        <option value="">—</option>
                        @foreach (['Sans diplôme', 'BFEM', 'Baccalauréat', 'BTS / DUT', 'Licence', 'Master', 'Doctorat'] as $niveau)
                            <option @selected(old('niveau_etude', $profil->niveau_etude) === $niveau)>{{ $niveau }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="annees_experience" class="form-label">Années d'expérience</label>
                    <input type="number" min="0" max="60" id="annees_experience" name="annees_experience" value="{{ old('annees_experience', $profil->annees_experience) }}" class="form-control @error('annees_experience') is-invalid @enderror">
                    @error('annees_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="disponibilite" class="form-label">Disponibilité</label>
                    <input type="text" id="disponibilite" name="disponibilite" value="{{ old('disponibilite', $profil->disponibilite) }}" class="form-control" placeholder="Immédiate, 1 mois…">
                </div>
                <div class="col-md-6">
                    <label for="competences" class="form-label">Compétences <small class="text-muted">(séparées par des virgules)</small></label>
                    <textarea id="competences" name="competences" rows="3" class="form-control">{{ old('competences', $profil->competences) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="langues" class="form-label">Langues</label>
                    <input type="text" id="langues" name="langues" value="{{ old('langues', $profil->langues) }}" class="form-control mb-3" placeholder="Français, wolof, anglais…">
                    <label for="pretention_salariale" class="form-label">Prétention salariale mensuelle (FCFA)</label>
                    <input type="number" min="0" step="5000" id="pretention_salariale" name="pretention_salariale" value="{{ old('pretention_salariale', $profil->pretention_salariale) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label for="presentation" class="form-label">Présentation</label>
                    <textarea id="presentation" name="presentation" rows="4" class="form-control" placeholder="Quelques lignes sur votre parcours et vos objectifs.">{{ old('presentation', $profil->presentation) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="linkedin" class="form-label">Profil LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" value="{{ old('linkedin', $profil->linkedin) }}" class="form-control @error('linkedin') is-invalid @enderror" placeholder="https://linkedin.com/in/…">
                    @error('linkedin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" role="switch" id="visible_recruteurs" name="visible_recruteurs" value="1" @checked(old('visible_recruteurs', $profil->visible_recruteurs))>
                <label class="form-check-label" for="visible_recruteurs">Rendre mon profil visible dans la recherche des recruteurs</label>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>

    <form method="POST" action="{{ route('password.update') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        @method('PUT')
        <div class="card-body p-4">
            <h2 class="h6 fw-bold mb-3">Mot de passe</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                    @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                    @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="password_confirmation" class="form-label">Confirmation</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>
            </div>
            <button type="submit" class="btn btn-outline-primary mt-3">Changer le mot de passe</button>
        </div>
    </form>
@endsection

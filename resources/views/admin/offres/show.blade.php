@extends('layouts.espace')

@section('titre', $offre->titre)
@section('sous_titre', $offre->company->nom.' · '.$offre->type_contrat->label().' · '.$offre->lieu)

@section('actions')
    <a href="{{ route('admin.offres.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Offres</a>
@endsection

@section('espace')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <x-statut-badge :statut="$offre->statut" />
                        <small class="text-muted">{{ $offre->applications_count }} candidature(s)</small>
                        @if ($offre->validateur)<small class="text-muted">· traitée par {{ $offre->validateur->name }}</small>@endif
                        @if ($offre->estVisible())<a href="{{ route('offres.show', $offre) }}" target="_blank" class="small ms-auto">Voir la page publique <i class="bi bi-box-arrow-up-right"></i></a>@endif
                    </div>
                    @if ($offre->motif_rejet)
                        <div class="alert alert-danger small"><strong>Motif du rejet :</strong> {{ $offre->motif_rejet }}</div>
                    @endif
                    <dl class="row small">
                        <dt class="col-sm-4 text-muted fw-normal">Postes</dt><dd class="col-sm-8">{{ $offre->nombre_postes }}</dd>
                        @if ($offre->secteur)<dt class="col-sm-4 text-muted fw-normal">Secteur</dt><dd class="col-sm-8">{{ $offre->secteur }}</dd>@endif
                        @if ($offre->niveau_etude)<dt class="col-sm-4 text-muted fw-normal">Niveau</dt><dd class="col-sm-8">{{ $offre->niveau_etude }}</dd>@endif
                        @if ($offre->experience_min !== null)<dt class="col-sm-4 text-muted fw-normal">Expérience</dt><dd class="col-sm-8">{{ $offre->experience_min }} an(s) minimum</dd>@endif
                        @if ($offre->salaireLibelle())<dt class="col-sm-4 text-muted fw-normal">Salaire</dt><dd class="col-sm-8">{{ $offre->salaireLibelle() }}</dd>@endif
                        @if ($offre->date_limite)<dt class="col-sm-4 text-muted fw-normal">Date limite</dt><dd class="col-sm-8">{{ $offre->date_limite->translatedFormat('d F Y') }}</dd>@endif
                    </dl>
                    <h2 class="h6 fw-bold">Description</h2>
                    <p class="text-pre-line">{{ $offre->description }}</p>
                    @if ($offre->profil_recherche)<h2 class="h6 fw-bold">Profil recherché</h2><p class="text-pre-line">{{ $offre->profil_recherche }}</p>@endif
                    @if ($offre->avantages)<h2 class="h6 fw-bold">Avantages</h2><p class="text-pre-line mb-0">{{ $offre->avantages }}</p>@endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body small">
                    <h2 class="h6 fw-bold">Entreprise</h2>
                    <a href="{{ route('admin.utilisateurs.show', $offre->company->user) }}" class="fw-semibold">{{ $offre->company->nom }}</a>
                    <div class="mt-1">
                        @if ($offre->company->est_verifiee)
                            <span class="badge text-bg-success">Vérifiée</span>
                        @else
                            <span class="badge text-bg-warning">Non vérifiée</span>
                        @endif
                    </div>
                    <div class="text-muted mt-2">{{ $offre->company->user->email }}<br>{{ $offre->company->telephone }}</div>
                </div>
            </div>
            @if (in_array($offre->statut, [\App\Enums\OfferStatus::EnAttente, \App\Enums\OfferStatus::Rejetee], true))
                <form method="POST" action="{{ route('admin.offres.valider', $offre) }}" class="mb-3">
                    @csrf @method('PATCH')
                    <button class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i> Valider et publier</button>
                </form>
            @endif
            @if (in_array($offre->statut, [\App\Enums\OfferStatus::EnAttente, \App\Enums\OfferStatus::Publiee], true))
                <form method="POST" action="{{ route('admin.offres.rejeter', $offre) }}" class="card border-0 shadow-sm" novalidate>
                    @csrf @method('PATCH')
                    <div class="card-body">
                        <label for="motif_rejet" class="form-label small fw-semibold">Rejeter / dépublier</label>
                        <textarea id="motif_rejet" name="motif_rejet" rows="3" class="form-control form-control-sm mb-2 @error('motif_rejet') is-invalid @enderror" placeholder="Motif communiqué à l'entreprise">{{ old('motif_rejet') }}</textarea>
                        @error('motif_rejet')<div class="invalid-feedback mb-2">{{ $message }}</div>@enderror
                        <button class="btn btn-outline-danger btn-sm w-100">Rejeter l'offre</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

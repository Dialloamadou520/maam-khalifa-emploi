@extends('layouts.espace')

@section('titre', $entreprise->nom)
@section('sous_titre', 'Votre espace recruteur')

@section('actions')
    @if (Route::has('recruteur.offres.create'))
        <a href="{{ route('recruteur.offres.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Publier une offre</a>
    @endif
@endsection

@section('espace')
    @unless ($entreprise->est_verifiee)
        <div class="alert alert-warning small">
            <i class="bi bi-hourglass-split me-1"></i> Votre compte entreprise est en cours de vérification par l'agence. Vos offres seront publiées après validation.
        </div>
    @endunless

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><x-stat-card libelle="Offres publiées" :valeur="$stats['publiees']" icone="briefcase" couleur="success" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="En attente de validation" :valeur="$stats['en_attente']" icone="hourglass-split" couleur="warning" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Candidatures reçues" :valeur="$stats['candidatures']" icone="people" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Besoins soumis" :valeur="$stats['besoins']" icone="clipboard-plus" couleur="info" /></div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Mes offres</div>
                <div class="list-group list-group-flush">
                    @forelse ($offres as $offre)
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <div class="min-w-0">
                                <a href="{{ route('recruteur.offres.show', $offre) }}" class="fw-semibold text-truncate d-block">{{ $offre->titre }}</a>
                                <small class="text-muted">{{ $offre->applications_count }} candidature(s) · {{ $offre->lieu }}</small>
                            </div>
                            <x-statut-badge :statut="$offre->statut" />
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Aucune offre pour le moment.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Dernières candidatures</div>
                <div class="list-group list-group-flush">
                    @forelse ($candidatures as $candidature)
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <div class="min-w-0">
                                <a href="{{ route('recruteur.candidatures.show', $candidature) }}" class="fw-semibold text-truncate d-block">{{ $candidature->candidat->name }}</a>
                                <small class="text-muted">{{ $candidature->jobOffer->titre }} · {{ $candidature->created_at->diffForHumans() }}</small>
                            </div>
                            <x-statut-badge :statut="$candidature->statut" />
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Aucune candidature reçue pour le moment.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

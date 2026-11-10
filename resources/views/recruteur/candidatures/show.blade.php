@extends('layouts.espace')

@php($candidat = $candidature->candidat)
@php($profil = $candidat->candidateProfile)

@section('titre', $candidat->name)
@section('sous_titre', 'Candidature à « '.$candidature->jobOffer->titre.' »')

@section('actions')
    <a href="{{ route('recruteur.offres.show', $candidature->jobOffer) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Retour à l'offre</a>
@endsection

@section('espace')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 fw-bold mb-1">{{ $profil?->titre_profil ?? 'Profil non renseigné' }}</h2>
                    <p class="text-muted small mb-3">
                        {{ collect([$profil?->ville, $profil?->niveau_etude, $profil?->annees_experience !== null ? $profil->annees_experience.' an(s) d\'expérience' : null])->filter()->join(' · ') }}
                    </p>
                    @if ($profil?->listeCompetences())
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach ($profil->listeCompetences() as $competence)
                                <span class="badge text-bg-light border">{{ $competence }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if ($profil?->presentation)<p class="text-pre-line">{{ $profil->presentation }}</p>@endif
                    <dl class="row small mb-0">
                        @if ($profil?->langues)<dt class="col-sm-4 text-muted fw-normal">Langues</dt><dd class="col-sm-8">{{ $profil->langues }}</dd>@endif
                        @if ($profil?->disponibilite)<dt class="col-sm-4 text-muted fw-normal">Disponibilité</dt><dd class="col-sm-8">{{ $profil->disponibilite }}</dd>@endif
                        @if ($profil?->pretention_salariale)<dt class="col-sm-4 text-muted fw-normal">Prétention</dt><dd class="col-sm-8">{{ fcfa($profil->pretention_salariale) }} / mois</dd>@endif
                        <dt class="col-sm-4 text-muted fw-normal">Email</dt><dd class="col-sm-8"><a href="mailto:{{ $candidat->email }}">{{ $candidat->email }}</a></dd>
                        @if ($candidat->telephone)<dt class="col-sm-4 text-muted fw-normal">Téléphone</dt><dd class="col-sm-8"><a href="tel:{{ $candidat->telephone }}">{{ $candidat->telephone }}</a></dd>@endif
                        @if ($profil?->linkedin)<dt class="col-sm-4 text-muted fw-normal">LinkedIn</dt><dd class="col-sm-8"><a href="{{ $profil->linkedin }}" target="_blank" rel="noopener">{{ $profil->linkedin }}</a></dd>@endif
                    </dl>
                </div>
            </div>
            @if ($candidature->message)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 fw-bold">Message du candidat</h2>
                        <p class="text-pre-line mb-0">{{ $candidature->message }}</p>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-grid gap-2">
                    @if ($candidature->cv)
                        <a href="{{ route('recruteur.candidatures.document', [$candidature, 'cv']) }}" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i> Télécharger le CV</a>
                    @endif
                    @if ($candidature->lettre)
                        <a href="{{ route('recruteur.candidatures.document', [$candidature, 'lettre']) }}" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i> Lettre de motivation</a>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('recruteur.candidatures.update', $candidature) }}" class="card border-0 shadow-sm" novalidate>
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <h2 class="h6 fw-bold">Suivi</h2>
                    <label for="statut" class="form-label small">Statut</label>
                    <select id="statut" name="statut" class="form-select mb-3 @error('statut') is-invalid @enderror">
                        @foreach (\App\Enums\ApplicationStatus::cases() as $s)
                            @continue($s === \App\Enums\ApplicationStatus::Envoyee)
                            <option value="{{ $s->value }}" @selected(old('statut', $candidature->statut->value) === $s->value)>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <label for="note_recruteur" class="form-label small">Note interne <small class="text-muted">(non visible du candidat)</small></label>
                    <textarea id="note_recruteur" name="note_recruteur" rows="3" class="form-control mb-3">{{ old('note_recruteur', $candidature->note_recruteur) }}</textarea>
                    <button class="btn btn-primary w-100">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
@endsection

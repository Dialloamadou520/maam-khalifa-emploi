@extends('layouts.espace')

@php($u = $utilisateur)
@php($profil = $u->candidateProfile)
@php($entreprise = $u->company)

@section('titre', $entreprise?->nom ?? $u->name)
@section('sous_titre', $u->role->label().' · inscrit le '.$u->created_at->translatedFormat('d F Y'))

@section('actions')
    <div class="d-flex flex-wrap gap-2">
        @if ($entreprise)
            <form method="POST" action="{{ route('admin.utilisateurs.verifier', $u) }}">
                @csrf @method('PATCH')
                <button class="btn {{ $entreprise->est_verifiee ? 'btn-outline-warning' : 'btn-success' }}">
                    <i class="bi bi-patch-check me-1"></i> {{ $entreprise->est_verifiee ? 'Retirer la vérification' : 'Vérifier l\'entreprise' }}
                </button>
            </form>
        @endif
        @if ($u->isCandidat())
            <a href="{{ route('admin.mises-en-relation.create', ['candidat' => $u->id]) }}" class="btn btn-outline-primary"><i class="bi bi-link-45deg"></i> Mettre en relation</a>
        @endif
        <form method="POST" action="{{ route('admin.utilisateurs.basculer', $u) }}" onsubmit="return confirm('{{ $u->est_actif ? 'Désactiver' : 'Réactiver' }} ce compte ?')">
            @csrf @method('PATCH')
            <button class="btn {{ $u->est_actif ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $u->est_actif ? 'Désactiver le compte' : 'Réactiver le compte' }}</button>
        </form>
    </div>
@endsection

@section('espace')
    @unless ($u->est_actif)
        <div class="alert alert-secondary small">Ce compte est désactivé : l'utilisateur ne peut plus accéder à son espace.</div>
    @endunless
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold">Coordonnées</h2>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-normal">Nom</dt><dd class="col-7">{{ $u->name }}</dd>
                        <dt class="col-5 text-muted fw-normal">Email</dt><dd class="col-7 text-break"><a href="mailto:{{ $u->email }}">{{ $u->email }}</a></dd>
                        <dt class="col-5 text-muted fw-normal">Téléphone</dt><dd class="col-7">{{ $u->telephone ?? '—' }}</dd>
                        @if ($entreprise)
                            @foreach (['secteur' => 'Secteur', 'taille' => 'Effectif', 'ninea' => 'NINEA', 'ville' => 'Ville', 'site_web' => 'Site web'] as $champ => $libelle)
                                <dt class="col-5 text-muted fw-normal">{{ $libelle }}</dt><dd class="col-7 text-break">{{ $entreprise->{$champ} ?? '—' }}</dd>
                            @endforeach
                        @endif
                        @if ($profil)
                            <dt class="col-5 text-muted fw-normal">Profil</dt><dd class="col-7">{{ $profil->titre_profil ?? '—' }}</dd>
                            <dt class="col-5 text-muted fw-normal">Ville</dt><dd class="col-7">{{ $profil->ville ?? '—' }}</dd>
                            <dt class="col-5 text-muted fw-normal">Niveau</dt><dd class="col-7">{{ $profil->niveau_etude ?? '—' }}</dd>
                            <dt class="col-5 text-muted fw-normal">Expérience</dt><dd class="col-7">{{ $profil->annees_experience !== null ? $profil->annees_experience.' an(s)' : '—' }}</dd>
                            <dt class="col-5 text-muted fw-normal">Visible recruteurs</dt><dd class="col-7">{{ $profil->visible_recruteurs ? 'Oui' : 'Non' }}</dd>
                            <dt class="col-5 text-muted fw-normal">Complétion</dt><dd class="col-7">{{ $profil->completion() }} %</dd>
                        @endif
                    </dl>
                </div>
            </div>
            @if ($u->isCandidat())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">Documents ({{ $u->documents->count() }})</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($u->documents as $document)
                            <li class="list-group-item d-flex justify-content-between align-items-center small">
                                <span class="text-truncate"><span class="badge text-bg-light border">{{ $document->type->label() }}</span> {{ $document->titre }}</span>
                                <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
                            </li>
                        @empty
                            <li class="list-group-item small text-muted">Aucun document.</li>
                        @endforelse
                    </ul>
                </div>
            @endif
        </div>
        <div class="col-lg-7">
            @if ($u->isCandidat())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Candidatures ({{ $u->jobApplications->count() }})</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($u->jobApplications as $c)
                            <li class="list-group-item d-flex justify-content-between align-items-center gap-2 small">
                                <span>{{ $c->jobOffer->titre }} <span class="text-muted">— {{ $c->jobOffer->company->nom }}</span></span>
                                <x-statut-badge :statut="$c->statut" />
                            </li>
                        @empty
                            <li class="list-group-item small text-muted">Aucune candidature.</li>
                        @endforelse
                    </ul>
                </div>
            @endif
            @if ($entreprise)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Offres ({{ $entreprise->jobOffers->count() }})</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($entreprise->jobOffers as $o)
                            <li class="list-group-item d-flex justify-content-between align-items-center gap-2 small">
                                <a href="{{ route('admin.offres.show', $o) }}">{{ $o->titre }}</a>
                                <span class="text-nowrap">{{ $o->applications_count }} cand. <x-statut-badge :statut="$o->statut" /></span>
                            </li>
                        @empty
                            <li class="list-group-item small text-muted">Aucune offre.</li>
                        @endforelse
                    </ul>
                </div>
            @endif
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Dossiers de prestations ({{ $u->serviceRequests->count() }})</div>
                <ul class="list-group list-group-flush">
                    @forelse ($u->serviceRequests as $d)
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2 small">
                            <a href="{{ route('admin.dossiers.show', $d) }}">{{ $d->reference }} — {{ $d->service->nom }}</a>
                            <x-statut-badge :statut="$d->statut" />
                        </li>
                    @empty
                        <li class="list-group-item small text-muted">Aucun dossier.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

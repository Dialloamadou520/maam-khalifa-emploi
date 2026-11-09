@extends('layouts.espace')

@section('titre', 'Bonjour, '.$user->name)
@section('sous_titre', 'Votre espace candidat')

@section('espace')
    @if ($completion < 100)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="fw-semibold">Profil complété à {{ $completion }} %</span>
                    @if (Route::has('candidat.profil.edit'))
                        <a href="{{ route('candidat.profil.edit') }}">Compléter mon profil</a>
                    @endif
                </div>
                <div class="progress" role="progressbar" aria-valuenow="{{ $completion }}" aria-valuemin="0" aria-valuemax="100" style="height: .5rem">
                    <div class="progress-bar bg-primary" style="width: {{ $completion }}%"></div>
                </div>
                <p class="small text-muted mb-0 mt-2">Un profil complet avec CV est plus visible par les recruteurs et par l'agence.</p>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><x-stat-card libelle="Candidatures" :valeur="$stats['candidatures']" icone="send-check" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Documents" :valeur="$stats['documents']" icone="file-earmark-person" couleur="info" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Dossiers" :valeur="$stats['dossiers']" icone="folder2-open" couleur="warning" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Mises en relation" :valeur="$stats['mises_en_relation']" icone="link-45deg" couleur="success" /></div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Mes dernières candidatures</div>
                <div class="list-group list-group-flush">
                    @forelse ($candidatures as $candidature)
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ $candidature->jobOffer->titre }}</div>
                                <small class="text-muted">{{ $candidature->jobOffer->company->nom }} · {{ $candidature->created_at->translatedFormat('d M Y') }}</small>
                            </div>
                            <x-statut-badge :statut="$candidature->statut" />
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Vous n'avez pas encore postulé. <a href="{{ route('offres.index') }}">Voir les offres</a></div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                    Offres récentes <a href="{{ route('offres.index') }}" class="small fw-normal">Tout voir</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($offres as $offre)
                        <a href="{{ route('offres.show', $offre) }}" class="list-group-item list-group-item-action">
                            <div class="fw-semibold">{{ $offre->titre }}</div>
                            <small class="text-muted">{{ $offre->company->nom }} · {{ $offre->lieu }} · {{ $offre->type_contrat->label() }}</small>
                        </a>
                    @empty
                        <div class="list-group-item text-muted small">Aucune offre publiée pour le moment.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.espace')

@section('titre', $profil->titre_profil ?? 'Profil candidat')
@section('sous_titre', $profil->user->name)

@section('actions')
    <a href="{{ route('recruteur.profils.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Recherche</a>
@endsection

@section('espace')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <dl class="row small">
                        @foreach (['domaine' => 'Domaine', 'niveau_etude' => 'Niveau d\'études', 'ville' => 'Ville', 'langues' => 'Langues', 'disponibilite' => 'Disponibilité'] as $champ => $libelle)
                            @if ($profil->{$champ})<dt class="col-sm-4 text-muted fw-normal">{{ $libelle }}</dt><dd class="col-sm-8">{{ $profil->{$champ} }}</dd>@endif
                        @endforeach
                        @if ($profil->annees_experience !== null)<dt class="col-sm-4 text-muted fw-normal">Expérience</dt><dd class="col-sm-8">{{ $profil->annees_experience }} an(s)</dd>@endif
                    </dl>
                    @if ($profil->listeCompetences())
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach ($profil->listeCompetences() as $c)
                                <span class="badge text-bg-light border">{{ $c }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if ($profil->presentation)<p class="text-pre-line mb-0">{{ $profil->presentation }}</p>@endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold">Mise en relation</h2>
                    <p class="small text-muted">Les coordonnées des candidats sont confidentielles. L'agence contacte le candidat et organise la rencontre.</p>
                    @if ($demande && in_array($demande->statut, [\App\Enums\MatchStatus::Proposee, \App\Enums\MatchStatus::Acceptee], true))
                        <div class="alert alert-info small mb-0">Demande en cours — <x-statut-badge :statut="$demande->statut" /></div>
                    @else
                        <form method="POST" action="{{ route('recruteur.profils.contacter', $profil) }}" novalidate>
                            @csrf
                            @if ($offres->isNotEmpty())
                                <label for="job_offer_id" class="form-label small">Pour l'offre</label>
                                <select id="job_offer_id" name="job_offer_id" class="form-select form-select-sm mb-2">
                                    <option value="">— Aucune en particulier —</option>
                                    @foreach ($offres as $o)
                                        <option value="{{ $o->id }}">{{ $o->titre }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <label for="commentaire" class="form-label small">Message pour l'agence</label>
                            <textarea id="commentaire" name="commentaire" rows="3" class="form-control form-control-sm mb-3"></textarea>
                            <button class="btn btn-primary w-100"><i class="bi bi-link-45deg me-1"></i> Demander une mise en relation</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

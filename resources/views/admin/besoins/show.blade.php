@extends('layouts.espace')

@section('titre', $besoin->intitule_poste)
@section('sous_titre', $besoin->company->nom.' · '.$besoin->nombre_postes.' poste(s) · '.$besoin->type_contrat->label())

@section('actions')
    <a href="{{ route('admin.besoins.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Besoins</a>
@endsection

@section('espace')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <p class="text-pre-line">{{ $besoin->description }}</p>
                    <dl class="row small mb-0">
                        @if ($besoin->lieu)<dt class="col-sm-4 text-muted fw-normal">Lieu</dt><dd class="col-sm-8">{{ $besoin->lieu }}</dd>@endif
                        @if ($besoin->competences_requises)<dt class="col-sm-4 text-muted fw-normal">Compétences</dt><dd class="col-sm-8 text-pre-line">{{ $besoin->competences_requises }}</dd>@endif
                        @if ($besoin->budget)<dt class="col-sm-4 text-muted fw-normal">Budget</dt><dd class="col-sm-8">{{ fcfa($besoin->budget) }} / mois</dd>@endif
                        @if ($besoin->date_souhaitee)<dt class="col-sm-4 text-muted fw-normal">Prise de poste</dt><dd class="col-sm-8">{{ $besoin->date_souhaitee->translatedFormat('d F Y') }}</dd>@endif
                        <dt class="col-sm-4 text-muted fw-normal">Reçu le</dt><dd class="col-sm-8">{{ $besoin->created_at->translatedFormat('d F Y') }}</dd>
                        @if ($besoin->traitePar)<dt class="col-sm-4 text-muted fw-normal">Suivi par</dt><dd class="col-sm-8">{{ $besoin->traitePar->name }}</dd>@endif
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Candidats proposés ({{ $besoin->matches->count() }})</div>
                <ul class="list-group list-group-flush">
                    @forelse ($besoin->matches as $match)
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2 small">
                            <span><a href="{{ route('admin.utilisateurs.show', $match->candidat) }}">{{ $match->candidat->name }}</a> <span class="text-muted">{{ $match->candidat->candidateProfile?->titre_profil }}</span></span>
                            <x-statut-badge :statut="$match->statut" />
                        </li>
                    @empty
                        <li class="list-group-item small text-muted">Aucun candidat proposé.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Proposer un candidat</div>
                <div class="card-body">
                    <form method="GET" class="d-flex gap-1 mb-3">
                        <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Poste, compétence, nom…" aria-label="Rechercher un candidat">
                        <button class="btn btn-sm btn-outline-primary" aria-label="Rechercher"><i class="bi bi-search"></i></button>
                    </form>
                    <ul class="list-group list-group-flush">
                        @forelse ($suggestions as $profil)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center gap-2 small">
                                <span class="min-w-0">
                                    <span class="fw-semibold">{{ $profil->user->name }}</span> — {{ $profil->titre_profil ?? 'profil à compléter' }}
                                    <span class="d-block text-muted">{{ collect([$profil->ville, $profil->niveau_etude, $profil->annees_experience !== null ? $profil->annees_experience.' an(s)' : null])->filter()->join(' · ') }}</span>
                                </span>
                                <form method="POST" action="{{ route('admin.mises-en-relation.store') }}">
                                    @csrf
                                    <input type="hidden" name="candidate_id" value="{{ $profil->user_id }}">
                                    <input type="hidden" name="recruitment_request_id" value="{{ $besoin->id }}">
                                    <button class="btn btn-sm btn-primary text-nowrap">Proposer</button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item px-0 small text-muted">Aucun candidat trouvé.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <form method="POST" action="{{ route('admin.besoins.update', $besoin) }}" class="card border-0 shadow-sm" novalidate>
                @csrf @method('PATCH')
                <div class="card-body">
                    <h2 class="h6 fw-bold">Traitement</h2>
                    <label for="statut" class="form-label small">Statut</label>
                    <select id="statut" name="statut" class="form-select mb-3">
                        @foreach (\App\Enums\RecruitmentRequestStatus::cases() as $s)
                            <option value="{{ $s->value }}" @selected($besoin->statut === $s)>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <label for="note_admin" class="form-label small">Message à l'entreprise</label>
                    <textarea id="note_admin" name="note_admin" rows="4" class="form-control mb-3">{{ old('note_admin', $besoin->note_admin) }}</textarea>
                    <button class="btn btn-primary w-100">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

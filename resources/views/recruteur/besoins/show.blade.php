@extends('layouts.espace')

@section('titre', $besoin->intitule_poste)
@section('sous_titre', $besoin->nombre_postes.' poste(s) · '.$besoin->type_contrat->label().($besoin->lieu ? ' · '.$besoin->lieu : ''))

@section('actions')
    <a href="{{ route('recruteur.besoins.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Mes besoins</a>
@endsection

@section('espace')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3"><x-statut-badge :statut="$besoin->statut" /> <small class="text-muted ms-2">soumis le {{ $besoin->created_at->translatedFormat('d M Y') }}</small></div>
                    <p class="text-pre-line">{{ $besoin->description }}</p>
                    <dl class="row small mb-0">
                        @if ($besoin->competences_requises)<dt class="col-sm-4 text-muted fw-normal">Compétences</dt><dd class="col-sm-8 text-pre-line">{{ $besoin->competences_requises }}</dd>@endif
                        @if ($besoin->budget)<dt class="col-sm-4 text-muted fw-normal">Budget</dt><dd class="col-sm-8">{{ fcfa($besoin->budget) }} / mois</dd>@endif
                        @if ($besoin->date_souhaitee)<dt class="col-sm-4 text-muted fw-normal">Prise de poste</dt><dd class="col-sm-8">{{ $besoin->date_souhaitee->translatedFormat('d F Y') }}</dd>@endif
                        @if ($besoin->note_admin)<dt class="col-sm-4 text-muted fw-normal">Message de l'agence</dt><dd class="col-sm-8 text-pre-line">{{ $besoin->note_admin }}</dd>@endif
                    </dl>
                </div>
            </div>
            @if ($besoin->statut === \App\Enums\RecruitmentRequestStatus::Soumise)
                <form method="POST" action="{{ route('recruteur.besoins.annuler', $besoin) }}" class="mt-3" onsubmit="return confirm('Annuler ce besoin ?')">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-sm btn-outline-danger">Annuler ce besoin</button>
                </form>
            @endif
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Profils proposés par l'agence</div>
                <ul class="list-group list-group-flush">
                    @forelse ($besoin->matches as $match)
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ $match->candidat->name }}</div>
                                <small class="text-muted">{{ $match->candidat->candidateProfile?->titre_profil }}</small>
                            </div>
                            <x-statut-badge :statut="$match->statut" />
                        </li>
                    @empty
                        <li class="list-group-item small text-muted">L'agence n'a pas encore proposé de profil.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

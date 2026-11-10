@extends('layouts.espace')

@section('titre', $offre->titre)
@section('sous_titre', $offre->type_contrat->label().' · '.$offre->lieu)

@section('actions')
    <div class="d-flex gap-2">
        @if ($offre->estVisible())
            <a href="{{ route('offres.show', $offre) }}" class="btn btn-outline-secondary" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Voir en ligne</a>
        @endif
        @if ($offre->statut !== \App\Enums\OfferStatus::Cloturee)
            <a href="{{ route('recruteur.offres.edit', $offre) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Modifier</a>
        @endif
    </div>
@endsection

@section('espace')
    <div class="d-flex align-items-center gap-2 mb-3">
        <x-statut-badge :statut="$offre->statut" />
        @if ($offre->publiee_le)<small class="text-muted">publiée le {{ $offre->publiee_le->translatedFormat('d M Y') }}</small>@endif
        @if ($offre->date_limite)<small class="text-muted">· clôture le {{ $offre->date_limite->translatedFormat('d M Y') }}</small>@endif
    </div>
    @if ($offre->statut === \App\Enums\OfferStatus::Rejetee && $offre->motif_rejet)
        <div class="alert alert-danger small"><strong>Motif du refus :</strong> {{ $offre->motif_rejet }}</div>
    @endif

    <div class="row g-3 mb-4">
        @foreach ($statuts as $s)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3 px-2">
                        <div class="fs-4 fw-bold text-{{ $s->couleur() }}">{{ (int) ($parStatut[$s->value] ?? 0) }}</div>
                        <div class="compteur-libelle text-muted">{{ $s->label() }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Candidatures ({{ $candidatures->count() }})</div>
        <ul class="list-group list-group-flush">
            @forelse ($candidatures as $candidature)
                <li class="list-group-item d-flex flex-wrap align-items-center gap-2">
                    <div class="min-w-0 flex-grow-1">
                        <a href="{{ route('recruteur.candidatures.show', $candidature) }}" class="fw-semibold">{{ $candidature->candidat->name }}</a>
                        <div class="small text-muted">{{ $candidature->candidat->candidateProfile?->titre_profil ?? 'Profil non renseigné' }} · {{ $candidature->created_at->diffForHumans() }}</div>
                    </div>
                    <x-statut-badge :statut="$candidature->statut" />
                </li>
            @empty
                <li class="list-group-item small text-muted">Aucune candidature pour le moment.</li>
            @endforelse
        </ul>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h6 fw-bold">Description</h2>
            <p class="text-pre-line">{{ $offre->description }}</p>
            @if ($offre->profil_recherche)
                <h2 class="h6 fw-bold">Profil recherché</h2>
                <p class="text-pre-line">{{ $offre->profil_recherche }}</p>
            @endif
            @if ($offre->salaireLibelle())
                <p class="mb-0"><i class="bi bi-cash me-1"></i> {{ $offre->salaireLibelle() }}</p>
            @endif
        </div>
    </div>

    @if (in_array($offre->statut, [\App\Enums\OfferStatus::Publiee, \App\Enums\OfferStatus::EnAttente], true))
        <form method="POST" action="{{ route('recruteur.offres.cloturer', $offre) }}" onsubmit="return confirm('Clôturer cette offre ? Elle ne sera plus visible.')">
            @csrf
            @method('PATCH')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-octagon me-1"></i> Clôturer l'offre</button>
        </form>
    @endif
@endsection

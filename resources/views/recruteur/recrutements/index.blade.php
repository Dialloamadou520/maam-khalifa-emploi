@extends('layouts.espace')

@section('titre', 'Suivi des recrutements')
@section('sous_titre', 'Avancement de vos candidatures et des mises en relation organisées par l\'agence.')

@section('espace')
    <div class="row g-3 mb-4">
        @foreach ($statuts as $s)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-{{ $s->couleur() }}">{{ $totaux[$s->value] }}</div>
                        <div class="small text-muted">{{ $s->label() }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Par offre</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Offre</th>
                        @foreach ($statuts as $s)<th class="text-center">{{ $s->label() }}</th>@endforeach
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offres as $offre)
                        <tr>
                            <td><a href="{{ route('recruteur.offres.show', $offre) }}" class="fw-semibold">{{ $offre->titre }}</a> <x-statut-badge :statut="$offre->statut" /></td>
                            @foreach ($statuts as $s)<td class="text-center">{{ $compteurs[$offre->id][$s->value] ?? 0 }}</td>@endforeach
                            <td class="text-nowrap text-muted">{{ $offre->nombre_postes }} poste(s)</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($statuts) + 2 }}" class="text-center text-muted py-4">Aucune offre publiée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Mises en relation</div>
        <ul class="list-group list-group-flush">
            @forelse ($misesEnRelation as $match)
                <li class="list-group-item d-flex flex-wrap align-items-center gap-2">
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-semibold">{{ $match->candidat->name }} <span class="small text-muted fw-normal">{{ $match->candidat->candidateProfile?->titre_profil }}</span></div>
                        <small class="text-muted">
                            {{ $match->jobOffer?->titre ?? $match->recruitmentRequest?->intitule_poste ?? 'Candidature spontanée' }} · {{ $match->created_at->translatedFormat('d M Y') }}
                        </small>
                    </div>
                    <x-statut-badge :statut="$match->statut" />
                    <form method="POST" action="{{ route('recruteur.recrutements.update', $match) }}" class="d-flex gap-1">
                        @csrf
                        @method('PATCH')
                        <select name="statut" class="form-select form-select-sm" aria-label="Statut">
                            @foreach ([\App\Enums\MatchStatus::Acceptee, \App\Enums\MatchStatus::Refusee, \App\Enums\MatchStatus::Embauche] as $s)
                                <option value="{{ $s->value }}" @selected($match->statut === $s)>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-outline-primary">OK</button>
                    </form>
                </li>
            @empty
                <li class="list-group-item small text-muted">Aucune mise en relation pour le moment. <a href="{{ route('recruteur.profils.index') }}">Rechercher des profils</a>.</li>
            @endforelse
        </ul>
    </div>
@endsection

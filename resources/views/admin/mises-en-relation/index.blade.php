@extends('layouts.espace')

@section('titre', 'Mises en relation')
@section('sous_titre', 'Candidats proposés aux entreprises et demandes de contact des recruteurs.')

@section('actions')
    <a href="{{ route('admin.mises-en-relation.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouvelle mise en relation</a>
@endsection

@section('espace')
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.mises-en-relation.index') }}" class="btn btn-sm {{ $statut ? 'btn-outline-secondary' : 'btn-primary' }}">Toutes</a>
        @foreach (\App\Enums\MatchStatus::cases() as $s)
            <a href="{{ route('admin.mises-en-relation.index', ['statut' => $s->value]) }}" class="btn btn-sm {{ $statut === $s->value ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $s->label() }}</a>
        @endforeach
    </div>
    <div class="card border-0 shadow-sm">
        <ul class="list-group list-group-flush">
            @forelse ($misesEnRelation as $m)
                <li class="list-group-item d-flex flex-wrap align-items-center gap-2 py-3">
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-semibold">
                            <a href="{{ route('admin.utilisateurs.show', $m->candidat) }}">{{ $m->candidat->name }}</a>
                            <i class="bi bi-arrow-left-right mx-1 text-muted"></i>
                            {{ $m->company->nom }}
                        </div>
                        <small class="text-muted">
                            {{ $m->jobOffer?->titre ?? $m->recruitmentRequest?->intitule_poste ?? 'Sans offre associée' }}
                            · {{ $m->created_at->translatedFormat('d M Y') }}
                            · {{ $m->creePar?->isRecruteur() ? 'demandée par l\'entreprise' : 'proposée par '.($m->creePar?->name ?? 'l\'agence') }}
                        </small>
                        @if ($m->commentaire)<div class="small text-pre-line mt-1">{{ $m->commentaire }}</div>@endif
                    </div>
                    <form method="POST" action="{{ route('admin.mises-en-relation.update', $m) }}" class="d-flex gap-1">
                        @csrf @method('PATCH')
                        <input type="hidden" name="commentaire" value="{{ $m->commentaire }}">
                        <select name="statut" class="form-select form-select-sm" aria-label="Statut">
                            @foreach (\App\Enums\MatchStatus::cases() as $s)
                                <option value="{{ $s->value }}" @selected($m->statut === $s)>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-outline-primary">OK</button>
                    </form>
                </li>
            @empty
                <li class="list-group-item text-center text-muted small py-4">Aucune mise en relation.</li>
            @endforelse
        </ul>
    </div>
    <div class="mt-3">{{ $misesEnRelation->links() }}</div>
@endsection

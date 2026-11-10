@extends('layouts.espace')

@section('titre', 'Offres d\'emploi')
@section('sous_titre', 'Validez les offres soumises par les entreprises avant publication.')

@section('espace')
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach (\App\Enums\OfferStatus::cases() as $s)
            <a href="{{ route('admin.offres.index', ['statut' => $s->value]) }}" class="btn btn-sm {{ $statut === $s->value ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $s->label() }} <span class="badge text-bg-light">{{ (int) ($compteurs[$s->value] ?? 0) }}</span>
            </a>
        @endforeach
        <a href="{{ route('admin.offres.index', ['statut' => 'toutes']) }}" class="btn btn-sm {{ $statut === 'toutes' ? 'btn-primary' : 'btn-outline-secondary' }}">Toutes</a>
        <form method="GET" class="ms-auto d-flex gap-1">
            <input type="hidden" name="statut" value="{{ $statut }}">
            <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Titre ou entreprise" aria-label="Rechercher">
            <button class="btn btn-sm btn-outline-primary" aria-label="Rechercher"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Offre</th><th>Entreprise</th><th>Soumise</th><th class="text-center">Cand.</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                    @forelse ($offres as $offre)
                        <tr>
                            <td><a href="{{ route('admin.offres.show', $offre) }}" class="fw-semibold">{{ $offre->titre }}</a><div class="small text-muted">{{ $offre->type_contrat->label() }} · {{ $offre->lieu }}</div></td>
                            <td class="small">{{ $offre->company->nom }} @unless ($offre->company->est_verifiee)<i class="bi bi-exclamation-triangle text-warning" title="Entreprise non vérifiée"></i>@endunless</td>
                            <td class="small text-nowrap">{{ $offre->updated_at->diffForHumans() }}</td>
                            <td class="text-center">{{ $offre->applications_count }}</td>
                            <td><x-statut-badge :statut="$offre->statut" /></td>
                            <td class="text-end"><a href="{{ route('admin.offres.show', $offre) }}" class="btn btn-sm btn-outline-primary">{{ $offre->statut === \App\Enums\OfferStatus::EnAttente ? 'Examiner' : 'Voir' }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Aucune offre dans cette catégorie.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $offres->links() }}</div>
@endsection

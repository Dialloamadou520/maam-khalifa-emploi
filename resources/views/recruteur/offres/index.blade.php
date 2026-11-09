@extends('layouts.espace')

@section('titre', 'Mes offres d\'emploi')
@section('sous_titre', 'Chaque offre est vérifiée par l\'agence avant publication.')

@section('actions')
    <a href="{{ route('recruteur.offres.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouvelle offre</a>
@endsection

@section('espace')
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('recruteur.offres.index') }}" class="btn btn-sm {{ $statut ? 'btn-outline-secondary' : 'btn-primary' }}">Toutes</a>
        @foreach (\App\Enums\OfferStatus::cases() as $s)
            <a href="{{ route('recruteur.offres.index', ['statut' => $s->value]) }}" class="btn btn-sm {{ $statut === $s->value ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $s->label() }}</a>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr><th>Offre</th><th>Contrat</th><th>Candidatures</th><th>Date limite</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    @forelse ($offres as $offre)
                        <tr>
                            <td><a href="{{ route('recruteur.offres.show', $offre) }}" class="fw-semibold">{{ $offre->titre }}</a><div class="small text-muted">{{ $offre->lieu }}</div></td>
                            <td class="small">{{ $offre->type_contrat->label() }}</td>
                            <td>{{ $offre->applications_count }}</td>
                            <td class="small text-nowrap">{{ $offre->date_limite?->translatedFormat('d M Y') ?? '—' }}</td>
                            <td><x-statut-badge :statut="$offre->statut" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted small py-4">Aucune offre. <a href="{{ route('recruteur.offres.create') }}">Publier votre première offre</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $offres->links() }}</div>
@endsection

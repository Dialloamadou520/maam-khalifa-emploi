@extends('layouts.espace')

@section('titre', 'Besoins en recrutement')
@section('sous_titre', 'Confiez un recrutement à l\'agence : elle recherche et présélectionne les profils pour vous.')

@section('actions')
    <a href="{{ route('recruteur.besoins.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouveau besoin</a>
@endsection

@section('espace')
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Poste</th><th>Postes</th><th>Profils proposés</th><th>Soumis le</th><th>Statut</th></tr></thead>
                <tbody>
                    @forelse ($besoins as $besoin)
                        <tr>
                            <td><a href="{{ route('recruteur.besoins.show', $besoin) }}" class="fw-semibold">{{ $besoin->intitule_poste }}</a><div class="small text-muted">{{ $besoin->type_contrat->label() }} · {{ $besoin->lieu ?? '—' }}</div></td>
                            <td>{{ $besoin->nombre_postes }}</td>
                            <td>{{ $besoin->matches_count }}</td>
                            <td class="small text-nowrap">{{ $besoin->created_at->translatedFormat('d M Y') }}</td>
                            <td><x-statut-badge :statut="$besoin->statut" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted small py-4">Aucun besoin soumis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $besoins->links() }}</div>
@endsection

@extends('layouts.espace')

@section('titre', 'Besoins en recrutement')
@section('sous_titre', 'Demandes confiées par les entreprises : proposez-leur des candidats.')

@section('espace')
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.besoins.index') }}" class="btn btn-sm {{ $statut ? 'btn-outline-secondary' : 'btn-primary' }}">Tous</a>
        @foreach (\App\Enums\RecruitmentRequestStatus::cases() as $s)
            <a href="{{ route('admin.besoins.index', ['statut' => $s->value]) }}" class="btn btn-sm {{ $statut === $s->value ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $s->label() }}</a>
        @endforeach
    </div>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Poste</th><th>Entreprise</th><th class="text-center">Postes</th><th class="text-center">Profils proposés</th><th>Reçu</th><th>Statut</th></tr></thead>
                <tbody>
                    @forelse ($besoins as $besoin)
                        <tr>
                            <td><a href="{{ route('admin.besoins.show', $besoin) }}" class="fw-semibold">{{ $besoin->intitule_poste }}</a><div class="small text-muted">{{ $besoin->type_contrat->label() }} · {{ $besoin->lieu ?? '—' }}</div></td>
                            <td class="small">{{ $besoin->company->nom }}</td>
                            <td class="text-center">{{ $besoin->nombre_postes }}</td>
                            <td class="text-center">{{ $besoin->matches_count }}</td>
                            <td class="small text-nowrap">{{ $besoin->created_at->diffForHumans() }}</td>
                            <td><x-statut-badge :statut="$besoin->statut" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Aucun besoin.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $besoins->links() }}</div>
@endsection

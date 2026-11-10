@extends('layouts.espace')

@section('titre', 'Recruteurs & entreprises')
@section('sous_titre', $utilisateurs->total().' compte(s)')

@section('espace')
    @include('admin.utilisateurs._filtres', ['avecEntreprise' => true])
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Entreprise</th><th>Contact</th><th class="text-center">Offres</th><th class="text-center">Besoins</th><th>Vérification</th><th></th></tr></thead>
                <tbody>
                    @forelse ($utilisateurs as $u)
                        <tr @class(['table-secondary' => ! $u->est_actif])>
                            <td><a href="{{ route('admin.utilisateurs.show', $u) }}" class="fw-semibold">{{ $u->company?->nom ?? '—' }}</a><div class="small text-muted">{{ $u->company?->secteur }}</div></td>
                            <td class="small">{{ $u->name }}<div class="text-muted">{{ $u->email }} · {{ $u->telephone }}</div></td>
                            <td class="text-center">{{ $u->company?->job_offers_count ?? 0 }}</td>
                            <td class="text-center">{{ $u->company?->recruitment_requests_count ?? 0 }}</td>
                            <td>
                                @if ($u->company?->est_verifiee)
                                    <span class="badge text-bg-success">Vérifiée</span>
                                @else
                                    <span class="badge text-bg-warning">À vérifier</span>
                                @endif
                            </td>
                            <td>@unless ($u->est_actif)<span class="badge text-bg-secondary">Désactivé</span>@endunless</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Aucun recruteur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $utilisateurs->links() }}</div>
@endsection

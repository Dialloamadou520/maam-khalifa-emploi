@extends('layouts.espace')

@section('titre', 'Candidats')
@section('sous_titre', $utilisateurs->total().' compte(s)')

@section('espace')
    @include('admin.utilisateurs._filtres', ['avecEntreprise' => false])
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Candidat</th><th>Profil</th><th class="text-center">Docs</th><th class="text-center">Candidatures</th><th class="text-center">Dossiers</th><th>Inscrit le</th><th></th></tr></thead>
                <tbody>
                    @forelse ($utilisateurs as $u)
                        <tr @class(['table-secondary' => ! $u->est_actif])>
                            <td><a href="{{ route('admin.utilisateurs.show', $u) }}" class="fw-semibold">{{ $u->name }}</a><div class="small text-muted">{{ $u->email }} · {{ $u->telephone }}</div></td>
                            <td class="small">{{ $u->candidateProfile?->titre_profil ?? '—' }}<div class="text-muted">Profil complété à {{ $u->candidateProfile?->completion() ?? 0 }} %</div></td>
                            <td class="text-center">{{ $u->documents_count }}</td>
                            <td class="text-center">{{ $u->job_applications_count }}</td>
                            <td class="text-center">{{ $u->service_requests_count }}</td>
                            <td class="small text-nowrap">{{ $u->created_at->translatedFormat('d M Y') }}</td>
                            <td>@unless ($u->est_actif)<span class="badge text-bg-secondary">Désactivé</span>@endunless</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted small py-4">Aucun candidat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $utilisateurs->links() }}</div>
@endsection

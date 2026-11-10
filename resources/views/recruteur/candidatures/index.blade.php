@extends('layouts.espace')

@section('titre', 'Candidatures reçues')
@section('sous_titre', 'Toutes les candidatures à vos offres.')

@section('espace')
    <form method="GET" class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-5">
                <label for="offre" class="form-label small">Offre</label>
                <select id="offre" name="offre" class="form-select form-select-sm">
                    <option value="">Toutes les offres</option>
                    @foreach ($offres as $o)
                        <option value="{{ $o->id }}" @selected((int) ($filtres['offre'] ?? 0) === $o->id)>{{ $o->titre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="statut" class="form-label small">Statut</label>
                <select id="statut" name="statut" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach (\App\Enums\ApplicationStatus::options() as $v => $l)
                        <option value="{{ $v }}" @selected(($filtres['statut'] ?? null) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-grid"><button class="btn btn-sm btn-primary">Filtrer</button></div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Candidat</th><th>Offre</th><th>Reçue le</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                    @forelse ($candidatures as $candidature)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $candidature->candidat->name }}</div>
                                <div class="small text-muted">{{ $candidature->candidat->candidateProfile?->titre_profil }}</div>
                            </td>
                            <td class="small">{{ $candidature->jobOffer->titre }}</td>
                            <td class="small text-nowrap">{{ $candidature->created_at->translatedFormat('d M Y') }}</td>
                            <td><x-statut-badge :statut="$candidature->statut" /></td>
                            <td class="text-end"><a href="{{ route('recruteur.candidatures.show', $candidature) }}" class="btn btn-sm btn-outline-primary">Examiner</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted small py-4">Aucune candidature.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $candidatures->links() }}</div>
@endsection

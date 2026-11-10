@extends('layouts.espace')

@section('titre', 'Dossiers')
@section('sous_titre', 'Toutes les demandes de prestations des candidats et des entreprises.')

@section('espace')
    <form method="GET" class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label small">Recherche</label>
                <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control form-control-sm" placeholder="Référence, nom, email…">
            </div>
            <div class="col-md-4">
                <label for="statut" class="form-label small">Statut</label>
                <select id="statut" name="statut" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach (\App\Enums\ServiceRequestStatus::options() as $v => $l)
                        <option value="{{ $v }}" @selected(($filtres['statut'] ?? null) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid"><button class="btn btn-sm btn-primary">Filtrer</button></div>
        </div>
    </form>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small"><tr><th>Référence</th><th>Client</th><th>Prestation</th><th>Montant</th><th>Paiement</th><th>Statut</th></tr></thead>
                <tbody>
                    @forelse ($dossiers as $dossier)
                        @php($paye = $dossier->payments->where('statut', \App\Enums\PaymentStatus::Payee)->sum('montant'))
                        <tr>
                            <td class="text-nowrap"><a href="{{ route('admin.dossiers.show', $dossier) }}" class="fw-semibold small">{{ $dossier->reference }}</a><div class="small text-muted">{{ $dossier->created_at->translatedFormat('d M Y') }}</div></td>
                            <td class="small">{{ $dossier->user->name }}<div class="text-muted">{{ $dossier->user->role->label() }}</div></td>
                            <td class="small">{{ $dossier->service->nom }}</td>
                            <td class="small text-nowrap">{{ fcfa($dossier->montant) }}</td>
                            <td>
                                @if ($paye >= $dossier->montant)
                                    <span class="badge text-bg-success">Payé</span>
                                @elseif ($paye > 0)
                                    <span class="badge text-bg-info">Partiel</span>
                                @else
                                    <span class="badge text-bg-warning">À régler</span>
                                @endif
                            </td>
                            <td><x-statut-badge :statut="$dossier->statut" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Aucun dossier.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $dossiers->links() }}</div>
@endsection

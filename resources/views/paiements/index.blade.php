@extends('layouts.espace')

@section('titre', 'Mes paiements')
@section('sous_titre', 'Historique des règlements de vos prestations.')

@section('espace')
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light small"><tr><th>Référence</th><th>Dossier</th><th>Montant</th><th>Moyen</th><th>Date</th><th>Statut</th></tr></thead>
                <tbody>
                    @forelse ($paiements as $p)
                        <tr>
                            <td class="small fw-semibold text-nowrap">{{ $p->reference }}</td>
                            <td class="small"><a href="{{ route($prefixe.'.dossiers.show', $p->serviceRequest) }}">{{ $p->serviceRequest->reference }}</a><div class="text-muted">{{ $p->serviceRequest->service->nom }}</div></td>
                            <td class="text-nowrap">{{ fcfa($p->montant) }}</td>
                            <td class="small">{{ $p->methode?->label() ?? "—" }}<div class="text-muted">{{ $p->transaction_id }}</div></td>
                            <td class="small text-nowrap">{{ ($p->payee_le ?? $p->created_at)->translatedFormat('d M Y') }}</td>
                            <td><x-statut-badge :statut="$p->statut" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Aucun paiement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $paiements->links() }}</div>
@endsection

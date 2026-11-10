@extends('layouts.espace')

@section('titre', 'Mes demandes & dossiers')
@section('sous_titre', 'Prestations demandées à l\'agence et leur état d\'avancement.')

@section('actions')
    <a href="{{ route($prefixe.'.dossiers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouvelle demande</a>
@endsection

@section('espace')
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr><th>Référence</th><th>Prestation</th><th>Montant</th><th>Paiement</th><th>Statut</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($dossiers as $dossier)
                        <tr>
                            <td class="text-nowrap small fw-semibold">{{ $dossier->reference }}</td>
                            <td>
                                {{ $dossier->service->nom }}
                                @if ($dossier->scholarship)<div class="small text-muted">{{ $dossier->scholarship->titre }}</div>@endif
                            </td>
                            <td class="text-nowrap">{{ fcfa($dossier->montant) }}</td>
                            <td>
                                @if ($dossier->estPaye())
                                    <span class="badge text-bg-success">Payé</span>
                                @else
                                    <span class="badge text-bg-warning">À régler</span>
                                @endif
                            </td>
                            <td><x-statut-badge :statut="$dossier->statut" /></td>
                            <td class="text-end"><a href="{{ route($prefixe.'.dossiers.show', $dossier) }}" class="btn btn-sm btn-outline-primary">Détails</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Aucune demande pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $dossiers->links() }}</div>
@endsection

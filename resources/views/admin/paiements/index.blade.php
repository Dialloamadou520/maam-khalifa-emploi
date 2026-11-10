@extends('layouts.espace')

@section('titre', 'Paiements')
@section('sous_titre', 'Confirmez les paiements déclarés et suivez les encaissements.')

@section('espace')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><x-stat-card libelle="Total encaissé" :valeur="fcfa($totaux['encaisse'])" icone="cash-coin" couleur="success" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Encaissé ce mois" :valeur="fcfa($totaux['mois'])" icone="calendar-check" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="À confirmer" :valeur="$totaux['en_attente']" icone="hourglass-split" couleur="warning" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Reste à encaisser" :valeur="fcfa($totaux['impaye'])" icone="exclamation-circle" couleur="danger" /></div>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-4"><input type="search" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control form-control-sm" placeholder="Référence, transaction, client…" aria-label="Recherche"></div>
            <div class="col-md-3">
                <select name="statut" class="form-select form-select-sm" aria-label="Statut">
                    <option value="">Tous statuts</option>
                    @foreach (\App\Enums\PaymentStatus::options() as $v => $l)<option value="{{ $v }}" @selected(($filtres['statut'] ?? null) === $v)>{{ $l }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="methode" class="form-select form-select-sm" aria-label="Moyen">
                    <option value="">Tous moyens</option>
                    @foreach (\App\Enums\PaymentMethod::options() as $v => $l)<option value="{{ $v }}" @selected(($filtres['methode'] ?? null) === $v)>{{ $l }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid"><button class="btn btn-sm btn-primary">Filtrer</button></div>
        </div>
    </form>

    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('admin.statistiques.export', ['type' => 'paiements']) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-filetype-csv me-1"></i> Exporter (Excel)</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light small"><tr><th>Référence</th><th>Client</th><th>Dossier</th><th>Montant</th><th>Moyen / transaction</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                    @forelse ($paiements as $p)
                        <tr>
                            <td class="small text-nowrap"><span class="fw-semibold">{{ $p->reference }}</span><div class="text-muted">{{ ($p->payee_le ?? $p->created_at)->translatedFormat('d M Y H:i') }}</div></td>
                            <td class="small">{{ $p->user->name }}</td>
                            <td class="small"><a href="{{ route('admin.dossiers.show', $p->serviceRequest) }}">{{ $p->serviceRequest->reference }}</a><div class="text-muted">{{ $p->serviceRequest->service->nom }}</div></td>
                            <td class="text-nowrap fw-semibold">{{ fcfa($p->montant) }}</td>
                            <td class="small">{{ $p->methode?->label() ?? '—' }}<div class="text-muted">{{ $p->transaction_id }} {{ $p->meta['telephone'] ?? '' }}</div></td>
                            <td><x-statut-badge :statut="$p->statut" /></td>
                            <td class="text-end text-nowrap">
                                @if ($p->statut === \App\Enums\PaymentStatus::EnAttente)
                                    <form method="POST" action="{{ route('admin.paiements.confirmer', $p) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-success" title="Confirmer"><i class="bi bi-check-lg"></i></button></form>
                                    <form method="POST" action="{{ route('admin.paiements.rejeter', $p) }}" class="d-inline" onsubmit="return confirm('Rejeter ce paiement ?')">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-danger" title="Rejeter"><i class="bi bi-x-lg"></i></button></form>
                                @elseif ($p->enregistrePar)
                                    <small class="text-muted">par {{ $p->enregistrePar->name }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted small py-4">Aucun paiement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $paiements->links() }}</div>
@endsection

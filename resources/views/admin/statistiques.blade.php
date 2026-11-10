@extends('layouts.espace')

@php($libelleMois = fn (string $m) => \Illuminate\Support\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M y'))
@php($maxCa = max(1, $chiffreAffaires->max()))

@section('titre', 'Statistiques & rapports')
@section('sous_titre', 'Activité de l\'agence sur les 12 derniers mois.')

@section('actions')
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-download me-1"></i> Exporter</button>
        <ul class="dropdown-menu dropdown-menu-end">
            @foreach (['paiements' => 'Paiements', 'dossiers' => 'Dossiers', 'candidatures' => 'Candidatures', 'offres' => 'Offres', 'candidats' => 'Candidats'] as $type => $libelle)
                <li><a class="dropdown-item" href="{{ route('admin.statistiques.export', ['type' => $type]) }}">{{ $libelle }} (CSV / Excel)</a></li>
            @endforeach
        </ul>
    </div>
@endsection

@section('espace')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between">
            <span>Chiffre d'affaires encaissé</span><span>{{ fcfa($chiffreAffaires->sum()) }}</span>
        </div>
        <div class="card-body overflow-x-auto">
            <div class="d-flex align-items-end gap-1 graphe-barres" role="img" aria-label="Chiffre d'affaires mensuel">
                @foreach ($chiffreAffaires as $m => $montant)
                    <div class="flex-fill text-center">
                        <div class="small text-muted graphe-valeur">{{ $montant ? number_format($montant / 1000, 0, ',', ' ').' k' : '' }}</div>
                        <div class="graphe-barre mx-auto" style="height: {{ max(2, round($montant / $maxCa * 140)) }}px" title="{{ fcfa($montant) }}"></div>
                        <div class="small text-muted mt-1">{{ $libelleMois($m) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Activité mensuelle</div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0 small">
                <thead class="table-light">
                    <tr><th></th>@foreach ($mois as $m)<th class="text-center text-nowrap">{{ $libelleMois($m) }}</th>@endforeach<th class="text-center">Total</th></tr>
                </thead>
                <tbody>
                    @foreach ($series as $libelle => $valeurs)
                        <tr>
                            <th class="text-nowrap fw-semibold">{{ $libelle }}</th>
                            @foreach ($valeurs as $v)<td class="text-center @if (! $v) text-muted @endif">{{ $v }}</td>@endforeach
                            <td class="text-center fw-bold">{{ $valeurs->sum() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4">
        @foreach (['Candidatures par statut' => $candidaturesParStatut, 'Mises en relation' => $misesEnRelation, 'Encaissements par moyen' => $paiementsParMethode] as $titre => $donnees)
            @php($total = max(1, $donnees->sum()))
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">{{ $titre }}</div>
                    <div class="card-body">
                        @forelse ($donnees as $libelle => $valeur)
                            <div class="d-flex justify-content-between small"><span>{{ $libelle }}</span><span class="fw-semibold">{{ $titre === 'Encaissements par moyen' ? fcfa($valeur) : $valeur }}</span></div>
                            <div class="progress mb-2" style="height: 6px"><div class="progress-bar" style="width: {{ round($valeur / $total * 100) }}%"></div></div>
                        @empty
                            <p class="small text-muted mb-0">Aucune donnée.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">Prestations les plus demandées</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light"><tr><th>Prestation</th><th class="text-center">Dossiers</th><th class="text-end">Montant facturé</th></tr></thead>
                <tbody>
                    @forelse ($prestations as $nom => $p)
                        <tr><td>{{ $nom }}</td><td class="text-center">{{ $p['dossiers'] }}</td><td class="text-end">{{ fcfa($p['montant']) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Aucun dossier.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

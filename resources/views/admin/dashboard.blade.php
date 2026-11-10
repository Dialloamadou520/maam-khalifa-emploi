@extends('layouts.espace')

@section('titre', 'Tableau de bord')
@section('sous_titre', 'Vue d\'ensemble de l\'activité de l\'agence')

@section('espace')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><x-stat-card libelle="Candidats" :valeur="$stats['candidats']" icone="person-lines-fill" :lien="Route::has('admin.candidats.index') ? route('admin.candidats.index') : null" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Recruteurs" :valeur="$stats['recruteurs']" icone="building" couleur="info" :lien="Route::has('admin.recruteurs.index') ? route('admin.recruteurs.index') : null" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Offres publiées" :valeur="$stats['offres_publiees']" icone="briefcase" couleur="success" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Offres à valider" :valeur="$stats['offres_en_attente']" icone="hourglass-split" couleur="warning" :lien="Route::has('admin.offres.index') ? route('admin.offres.index', ['statut' => 'en_attente']) : null" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Candidatures" :valeur="$stats['candidatures']" icone="send-check" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Besoins en recrutement" :valeur="$stats['besoins']" icone="clipboard-check" couleur="info" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Dossiers ouverts" :valeur="$stats['dossiers_ouverts']" icone="folder2-open" couleur="warning" /></div>
        <div class="col-6 col-xl-3"><x-stat-card libelle="Mises en relation" :valeur="$stats['mises_en_relation']" icone="link-45deg" couleur="success" /></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6"><x-stat-card libelle="Encaissé ce mois-ci" :valeur="fcfa($stats['encaisse_mois'])" icone="cash-coin" couleur="success" /></div>
        <div class="col-md-6"><x-stat-card libelle="Total encaissé" :valeur="fcfa($stats['encaisse'])" icone="bank" /></div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Offres en attente de validation</div>
                <div class="list-group list-group-flush">
                    @forelse ($offresEnAttente as $offre)
                        <div class="list-group-item">
                            <a href="{{ route('admin.offres.show', $offre) }}" class="fw-semibold">{{ $offre->titre }}</a>
                            <small class="text-muted">{{ $offre->company->nom }} · soumise {{ $offre->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Aucune offre en attente.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Derniers inscrits</div>
                <div class="list-group list-group-flush">
                    @forelse ($derniersInscrits as $inscrit)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ $inscrit->name }}</div>
                                <small class="text-muted">{{ $inscrit->email }} · {{ $inscrit->created_at->diffForHumans() }}</small>
                            </div>
                            <span class="badge text-bg-{{ $inscrit->role->couleur() }}">{{ $inscrit->role->label() }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-muted small">Aucun inscrit pour le moment.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Activité récente</div>
                <ul class="list-group list-group-flush">
                    @forelse ($activites as $activite)
                        <li class="list-group-item small d-flex justify-content-between gap-2">
                            <span>{{ $activite->description }}</span>
                            <span class="text-muted text-nowrap">{{ $activite->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">Aucune activité enregistrée.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

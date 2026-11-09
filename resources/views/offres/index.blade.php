@extends('layouts.base')

@section('titre', 'Offres d\'emploi')

@section('contenu')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Offres d'emploi</h1>
            <p class="text-muted mb-0">{{ $offres->total() }} offre(s) vérifiée(s) par l'agence</p>
        </div>
    </div>

    <form method="GET" action="{{ route('offres.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-5">
                <label for="q" class="form-label small text-muted mb-1">Mots-clés</label>
                <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Métier, compétence, secteur…">
            </div>
            <div class="col-6 col-md-3">
                <label for="contrat" class="form-label small text-muted mb-1">Contrat</label>
                <select id="contrat" name="contrat" class="form-select">
                    <option value="">Tous</option>
                    @foreach (\App\Enums\ContractType::options() as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(($filtres['contrat'] ?? '') === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="lieu" class="form-label small text-muted mb-1">Ville</label>
                <input type="text" id="lieu" name="lieu" value="{{ $filtres['lieu'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> Filtrer</button>
            </div>
        </div>
    </form>

    <div class="row g-3">
        @forelse ($offres as $offre)
            <div class="col-md-6 col-lg-4">
                <x-offre-card :offre="$offre" />
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">Aucune offre ne correspond à votre recherche.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $offres->links() }}</div>
@endsection

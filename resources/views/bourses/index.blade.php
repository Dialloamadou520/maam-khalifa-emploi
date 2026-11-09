@extends('layouts.base')

@section('titre', 'Bourses & financements')

@section('contenu')
    <div class="mb-3">
        <h1 class="h3 fw-bold mb-1">Bourses &amp; financements</h1>
        <p class="text-muted mb-0">Bourses d'études, financements de projets et formations financées, sélectionnés par l'agence.</p>
    </div>

    <form method="GET" action="{{ route('bourses.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label small text-muted mb-1">Recherche</label>
                <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Intitulé, organisme, pays…">
            </div>
            <div class="col-md-4">
                <label for="type" class="form-label small text-muted mb-1">Type</label>
                <select id="type" name="type" class="form-select">
                    <option value="">Tous</option>
                    @foreach (\App\Enums\ScholarshipType::options() as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(($filtres['type'] ?? '') === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> Filtrer</button>
            </div>
        </div>
    </form>

    <div class="row g-3">
        @forelse ($bourses as $bourse)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 carte-offre">
                    <div class="card-body d-flex flex-column">
                        <span class="badge text-bg-{{ $bourse->type->couleur() }} align-self-start mb-2">{{ $bourse->type->label() }}</span>
                        <h2 class="h6 fw-bold mb-1">
                            <a href="{{ route('bourses.show', $bourse) }}" class="stretched-link text-reset text-decoration-none">{{ $bourse->titre }}</a>
                        </h2>
                        <div class="small text-muted mb-3">{{ $bourse->organisme }}</div>
                        <div class="mt-auto small d-flex flex-wrap gap-3 text-muted">
                            @if ($bourse->pays)<span><i class="bi bi-globe"></i> {{ $bourse->pays }}</span>@endif
                            @if ($bourse->niveau)<span><i class="bi bi-mortarboard"></i> {{ $bourse->niveau }}</span>@endif
                            @if ($bourse->date_limite)<span><i class="bi bi-hourglass-split"></i> {{ $bourse->date_limite->translatedFormat('d M Y') }}</span>@endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">Aucune bourse ne correspond à votre recherche.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $bourses->links() }}</div>
@endsection

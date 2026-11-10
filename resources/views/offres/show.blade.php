@extends('layouts.base')

@section('titre', $offre->titre)
@section('description', \Illuminate\Support\Str::limit(strip_tags($offre->description), 150))

@section('contenu')
    <nav aria-label="Fil d'Ariane" class="small mb-3">
        <a href="{{ route('offres.index') }}"><i class="bi bi-arrow-left"></i> Toutes les offres</a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <span class="badge text-bg-{{ $offre->type_contrat->couleur() }} mb-2">{{ $offre->type_contrat->label() }}</span>
                    <h1 class="h3 fw-bold mb-1">{{ $offre->titre }}</h1>
                    <div class="text-muted mb-3">{{ $offre->company->nom }} · {{ $offre->lieu }}</div>

                    <h2 class="h6 fw-bold mt-4">Description du poste</h2>
                    <div class="text-pre-line">{{ $offre->description }}</div>

                    @if ($offre->profil_recherche)
                        <h2 class="h6 fw-bold mt-4">Profil recherché</h2>
                        <div class="text-pre-line">{{ $offre->profil_recherche }}</div>
                    @endif

                    @if ($offre->avantages)
                        <h2 class="h6 fw-bold mt-4">Avantages</h2>
                        <div class="text-pre-line">{{ $offre->avantages }}</div>
                    @endif
                </div>
            </article>
        </div>

        <aside class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <dl class="row small mb-3">
                        @if ($offre->salaireLibelle())
                            <dt class="col-5 text-muted">Salaire</dt><dd class="col-7">{{ $offre->salaireLibelle() }}</dd>
                        @endif
                        @if ($offre->niveau_etude)
                            <dt class="col-5 text-muted">Niveau</dt><dd class="col-7">{{ $offre->niveau_etude }}</dd>
                        @endif
                        @if ($offre->experience_min !== null)
                            <dt class="col-5 text-muted">Expérience</dt><dd class="col-7">{{ $offre->experience_min }} an(s) minimum</dd>
                        @endif
                        <dt class="col-5 text-muted">Postes</dt><dd class="col-7">{{ $offre->nombre_postes }}</dd>
                        @if ($offre->date_limite)
                            <dt class="col-5 text-muted">Date limite</dt><dd class="col-7">{{ $offre->date_limite->translatedFormat('d F Y') }}</dd>
                        @endif
                    </dl>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">Se connecter pour postuler</a>
                        <a href="{{ route('register') }}" class="btn btn-link w-100 btn-sm">Pas encore de compte ? Inscrivez-vous</a>
                    @else
                        @if ($dejaPostule)
                            <div class="alert alert-success small mb-0"><i class="bi bi-check-circle"></i> Vous avez déjà postulé à cette offre.</div>
                        @elseif (auth()->user()->isCandidat() && Route::has('candidat.candidatures.create'))
                            <a href="{{ route('candidat.candidatures.create', $offre) }}" class="btn btn-primary w-100">Postuler à cette offre</a>
                        @elseif (auth()->user()->isCandidat())
                            <button class="btn btn-primary w-100" disabled>Candidature bientôt disponible</button>
                        @else
                            <div class="small text-muted">La candidature est réservée aux comptes candidats.</div>
                        @endif
                    @endguest
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="fw-semibold mb-1">{{ $offre->company->nom }}</div>
                    @if ($offre->company->secteur)
                        <div class="small text-muted mb-2">{{ $offre->company->secteur }}</div>
                    @endif
                    @if ($offre->company->description)
                        <p class="small mb-0">{{ \Illuminate\Support\Str::limit($offre->company->description, 220) }}</p>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    @if ($similaires->isNotEmpty())
        <h2 class="h5 fw-bold mt-5 mb-3">Offres similaires</h2>
        <div class="row g-3">
            @foreach ($similaires as $similaire)
                <div class="col-md-4"><x-offre-card :offre="$similaire" /></div>
            @endforeach
        </div>
    @endif
@endsection

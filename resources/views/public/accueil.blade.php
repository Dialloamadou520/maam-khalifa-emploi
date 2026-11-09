@extends('layouts.base')

@section('titre', 'Emploi, recrutement et accompagnement')

@section('hero')
    <section class="hero py-5">
        <div class="container py-lg-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge bg-white text-primary mb-3">Agence Maam Khalifa</span>
                    <h1 class="display-5 fw-bold mb-3">Trouvez le bon emploi. Recrutez les bons talents.</h1>
                    <p class="lead mb-4">Offres d'emploi, bourses et financements, accompagnement personnalisé : une seule plateforme pour les candidats et les entreprises.</p>
                    <form action="{{ route('offres.index') }}" method="GET" class="bg-white rounded-3 p-2 d-flex flex-column flex-sm-row gap-2 shadow">
                        <input type="search" name="q" class="form-control border-0" placeholder="Métier, compétence, secteur…" aria-label="Rechercher une offre">
                        <input type="text" name="lieu" class="form-control border-0" placeholder="Ville" aria-label="Ville">
                        <button class="btn btn-primary px-4 text-nowrap" type="submit"><i class="bi bi-search me-1"></i> Rechercher</button>
                    </form>
                </div>
                <div class="col-lg-5">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-3 p-3">
                                <div class="fs-3 fw-bold">{{ $chiffres['offres'] }}</div>
                                <div class="small opacity-75">offres actives</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-3 p-3">
                                <div class="fs-3 fw-bold">{{ $chiffres['entreprises'] }}</div>
                                <div class="small opacity-75">entreprises</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-white bg-opacity-10 rounded-3 p-3">
                                <div class="fs-3 fw-bold">{{ $chiffres['candidats'] }}</div>
                                <div class="small opacity-75">candidats</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('contenu')
    <section class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <span class="icone-espace bg-primary-subtle text-primary mb-3"><i class="bi bi-person-badge"></i></span>
                    <h2 class="h4 fw-bold">Espace candidat</h2>
                    <ul class="text-muted mb-4">
                        <li>Profil professionnel, CV et lettres de motivation</li>
                        <li>Candidature en ligne aux offres validées par l'agence</li>
                        <li>Bourses, financements et suivi de vos dossiers</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary">Créer mon profil</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <span class="icone-espace bg-warning-subtle text-accent mb-3"><i class="bi bi-building"></i></span>
                    <h2 class="h4 fw-bold">Espace recruteur</h2>
                    <ul class="text-muted mb-4">
                        <li>Publication d'offres et soumission de besoins</li>
                        <li>Consultation des candidatures reçues</li>
                        <li>Recherche de profils et suivi des recrutements</li>
                    </ul>
                    <a href="{{ route('register', ['profil' => 'recruteur']) }}" class="btn btn-accent">Recruter avec l'agence</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h2 class="h4 fw-bold mb-0">Dernières offres d'emploi</h2>
            <a href="{{ route('offres.index') }}" class="small">Toutes les offres <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @forelse ($offres as $offre)
                <div class="col-md-6 col-lg-4">
                    <x-offre-card :offre="$offre" />
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border mb-0">Aucune offre publiée pour le moment. Revenez bientôt !</div>
                </div>
            @endforelse
        </div>
    </section>

    @if ($bourses->isNotEmpty())
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <h2 class="h4 fw-bold mb-0">Bourses &amp; financements</h2>
                <a href="{{ route('bourses.index') }}" class="small">Voir tout <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-3">
                @foreach ($bourses as $bourse)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 carte-offre">
                            <div class="card-body">
                                <span class="badge text-bg-{{ $bourse->type->couleur() }} mb-2">{{ $bourse->type->label() }}</span>
                                <h3 class="h6 fw-bold mb-1">
                                    <a href="{{ route('bourses.show', $bourse) }}" class="stretched-link text-reset text-decoration-none">{{ $bourse->titre }}</a>
                                </h3>
                                <div class="small text-muted">{{ $bourse->organisme }} @if($bourse->pays) · {{ $bourse->pays }} @endif</div>
                                @if ($bourse->date_limite)
                                    <div class="small mt-2"><i class="bi bi-hourglass-split"></i> Clôture le {{ $bourse->date_limite->translatedFormat('d F Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="card border-0 shadow-sm">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Besoin d'un accompagnement ?</h2>
                <p class="text-muted mb-0">CV, lettre de motivation, coaching, dossier de bourse, recrutement clé en main…</p>
            </div>
            <a href="{{ route('prestations') }}" class="btn btn-outline-primary">Découvrir nos prestations</a>
        </div>
    </section>
@endsection

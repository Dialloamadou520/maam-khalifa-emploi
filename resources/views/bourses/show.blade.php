@extends('layouts.base')

@section('titre', $bourse->titre)

@section('contenu')
    <nav aria-label="Fil d'Ariane" class="small mb-3">
        <a href="{{ route('bourses.index') }}"><i class="bi bi-arrow-left"></i> Toutes les bourses</a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <span class="badge text-bg-{{ $bourse->type->couleur() }} mb-2">{{ $bourse->type->label() }}</span>
                    <h1 class="h3 fw-bold mb-1">{{ $bourse->titre }}</h1>
                    <div class="text-muted mb-3">{{ $bourse->organisme }}</div>
                    <div class="text-pre-line">{{ $bourse->description }}</div>
                    @if ($bourse->conditions)
                        <h2 class="h6 fw-bold mt-4">Conditions d'éligibilité</h2>
                        <div class="text-pre-line">{{ $bourse->conditions }}</div>
                    @endif
                </div>
            </article>
        </div>
        <aside class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <dl class="row small mb-3">
                        @if ($bourse->pays)<dt class="col-5 text-muted">Pays</dt><dd class="col-7">{{ $bourse->pays }}</dd>@endif
                        @if ($bourse->niveau)<dt class="col-5 text-muted">Niveau</dt><dd class="col-7">{{ $bourse->niveau }}</dd>@endif
                        @if ($bourse->domaine)<dt class="col-5 text-muted">Domaine</dt><dd class="col-7">{{ $bourse->domaine }}</dd>@endif
                        @if ($bourse->montant)<dt class="col-5 text-muted">Montant</dt><dd class="col-7">{{ $bourse->montant }}</dd>@endif
                        @if ($bourse->date_limite)<dt class="col-5 text-muted">Date limite</dt><dd class="col-7">{{ $bourse->date_limite->translatedFormat('d F Y') }}</dd>@endif
                    </dl>
                    @if ($bourse->lien_externe)
                        <a href="{{ $bourse->lien_externe }}" target="_blank" rel="noopener" class="btn btn-outline-primary w-100 mb-2">Site officiel <i class="bi bi-box-arrow-up-right"></i></a>
                    @endif
                    <p class="small text-muted mb-0">L'agence peut monter votre dossier : voir la prestation « Accompagnement dossier de bourse » dans <a href="{{ route('prestations') }}">nos prestations</a>.</p>
                </div>
            </div>
        </aside>
    </div>
@endsection

@extends('layouts.base')

@section('titre', 'L\'agence')

@section('contenu')
    <div class="row g-4">
        <div class="col-lg-7">
            <h1 class="h3 fw-bold">Agence Maam Khalifa</h1>
            <p class="lead text-muted">Emploi, recrutement et accompagnement professionnel.</p>
            <p>L'Agence Maam Khalifa accompagne les candidats dans leur insertion professionnelle et aide les entreprises à trouver les profils dont elles ont besoin. Chaque offre publiée sur la plateforme est vérifiée par notre équipe avant sa mise en ligne.</p>
            <div class="row g-3 mt-2">
                @foreach ([['shield-check', 'Offres vérifiées', 'Toutes les offres sont validées par l\'agence.'], ['people', 'Mise en relation', 'Nous présentons les bons profils aux bonnes entreprises.'], ['mortarboard', 'Bourses & financements', 'Une veille et un accompagnement pour vos dossiers.']] as [$icone, $titre, $texte])
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="bi bi-{{ $icone }} fs-3 text-primary"></i>
                                <div class="fw-semibold mt-2">{{ $titre }}</div>
                                <div class="small text-muted">{{ $texte }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Nous contacter</h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-geo-alt text-primary me-2"></i> Dakar, Sénégal</li>
                        <li class="mb-2"><i class="bi bi-envelope text-primary me-2"></i> {{ config('agence.email') }}</li>
                        <li><i class="bi bi-telephone text-primary me-2"></i> {{ config('agence.telephone') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

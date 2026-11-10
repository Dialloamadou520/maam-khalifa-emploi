@extends('layouts.base')

@section('titre', 'Nos prestations')

@section('contenu')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Nos prestations</h1>
        <p class="text-muted mb-0">Des services d'accompagnement aux tarifs transparents, payables en ligne ou à l'agence.</p>
    </div>

    @foreach (['Pour les candidats' => $pourCandidats, 'Pour les entreprises' => $pourEntreprises] as $titre => $services)
        <h2 class="h5 fw-bold mt-4 mb-3">{{ $titre }}</h2>
        <div class="row g-3">
            @forelse ($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h3 class="h6 fw-bold">{{ $service->nom }}</h3>
                            <p class="small text-muted flex-grow-1">{{ $service->description }}</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <span class="fs-5 fw-bold text-primary">{{ fcfa($service->prix) }}</span>
                                @if ($service->delai)
                                    <small class="text-muted"><i class="bi bi-clock"></i> {{ $service->delai }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">Aucune prestation disponible pour le moment.</div>
            @endforelse
        </div>
    @endforeach

    <div class="text-center mt-5">
        <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="btn btn-primary">
            {{ auth()->check() ? 'Faire une demande depuis mon espace' : 'Créer un compte pour commander' }}
        </a>
    </div>
@endsection

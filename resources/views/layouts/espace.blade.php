{{-- Mise en page des espaces connectés : menu latéral propre au rôle + contenu. --}}
@extends('layouts.base')

@php
    $menus = [
        'candidat' => [
            ['candidat.dashboard', 'speedometer2', 'Tableau de bord'],
            ['candidat.profil.edit', 'person-badge', 'Mon profil'],
            ['candidat.documents.index', 'file-earmark-person', 'CV & lettres'],
            ['offres.index', 'briefcase', 'Offres d\'emploi'],
            ['candidat.candidatures.index', 'send-check', 'Mes candidatures'],
            ['bourses.index', 'mortarboard', 'Bourses & financements'],
            ['candidat.dossiers.index', 'folder2-open', 'Mes demandes & dossiers'],
            ['candidat.paiements.index', 'credit-card', 'Paiements'],
        ],
        'recruteur' => [
            ['recruteur.dashboard', 'speedometer2', 'Tableau de bord'],
            ['recruteur.entreprise.edit', 'building', 'Mon entreprise'],
            ['recruteur.offres.index', 'briefcase', 'Mes offres'],
            ['recruteur.besoins.index', 'clipboard-plus', 'Besoins en recrutement'],
            ['recruteur.candidatures.index', 'people', 'Candidatures reçues'],
            ['recruteur.profils.index', 'search', 'Rechercher des profils'],
            ['recruteur.recrutements.index', 'diagram-3', 'Suivi des recrutements'],
            ['recruteur.dossiers.index', 'folder2-open', 'Prestations & dossiers'],
            ['recruteur.paiements.index', 'credit-card', 'Paiements'],
        ],
        'admin' => [
            ['admin.dashboard', 'speedometer2', 'Tableau de bord'],
            ['admin.candidats.index', 'person-lines-fill', 'Candidats'],
            ['admin.recruteurs.index', 'building', 'Recruteurs'],
            ['admin.offres.index', 'briefcase', 'Offres d\'emploi'],
            ['admin.besoins.index', 'clipboard-check', 'Besoins en recrutement'],
            ['admin.dossiers.index', 'folder2-open', 'Dossiers'],
            ['admin.bourses.index', 'mortarboard', 'Bourses & financements'],
            ['admin.prestations.index', 'tags', 'Prestations & tarifs'],
            ['admin.paiements.index', 'cash-coin', 'Paiements'],
            ['admin.mises-en-relation.index', 'link-45deg', 'Mises en relation'],
            ['admin.statistiques', 'bar-chart-line', 'Statistiques & rapports'],
        ],
    ];
    $role = auth()->user()->role;
    $items = collect($menus[$role->value])->filter(fn ($item) => Route::has($item[0]));
@endphp

@section('contenu')
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="card border-0 shadow-sm espace-menu">
                <div class="card-body d-flex align-items-center gap-2 border-bottom">
                    <span class="avatar-initiales">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="lh-sm overflow-hidden">
                        <div class="fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                        <span class="badge text-bg-{{ $role->couleur() }}">Espace {{ mb_strtolower($role->label()) }}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary ms-auto d-lg-none" type="button"
                            data-bs-toggle="collapse" data-bs-target="#menuEspace" aria-expanded="false" aria-label="Menu de l'espace">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
                <nav class="collapse d-lg-block" id="menuEspace">
                    <div class="list-group list-group-flush">
                        @foreach ($items as [$route, $icone, $libelle])
                            @php($actif = request()->routeIs($route) || request()->routeIs(\Illuminate\Support\Str::beforeLast($route, '.').'.*') && ! str_ends_with($route, 'dashboard'))
                            <a href="{{ route($route) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 @if($actif) active @endif">
                                <i class="bi bi-{{ $icone }}"></i> {{ $libelle }}
                            </a>
                        @endforeach
                    </div>
                </nav>
            </div>
        </aside>

        <div class="col-lg-9">
            @include('layouts.partials.alerts')
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-0">@yield('titre')</h1>
                    @hasSection('sous_titre')
                        <p class="text-muted mb-0">@yield('sous_titre')</p>
                    @endif
                </div>
                @yield('actions')
            </div>
            @yield('espace')
        </div>
    </div>
@endsection

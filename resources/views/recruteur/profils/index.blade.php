@extends('layouts.espace')

@section('titre', 'Rechercher des profils')
@section('sous_titre', 'Candidats inscrits auprès de l\'agence et ouverts aux opportunités.')

@section('espace')
    <form method="GET" class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-4">
                <label for="q" class="form-label small">Mots-clés</label>
                <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control form-control-sm" placeholder="Poste, compétence…">
            </div>
            <div class="col-md-2">
                <label for="domaine" class="form-label small">Domaine</label>
                <input type="text" id="domaine" name="domaine" value="{{ $filtres['domaine'] ?? '' }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label for="niveau" class="form-label small">Niveau</label>
                <select id="niveau" name="niveau" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach ($niveaux as $n)
                        <option @selected(($filtres['niveau'] ?? null) === $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label for="experience" class="form-label small">Exp. min</label>
                <input type="number" min="0" id="experience" name="experience" value="{{ $filtres['experience'] ?? '' }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label for="ville" class="form-label small">Ville</label>
                <input type="text" id="ville" name="ville" value="{{ $filtres['ville'] ?? '' }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-1 d-grid"><button class="btn btn-sm btn-primary" aria-label="Rechercher"><i class="bi bi-search"></i></button></div>
        </div>
    </form>

    <p class="small text-muted">{{ $profils->total() }} profil(s)</p>
    <div class="row g-3">
        @forelse ($profils as $profil)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('recruteur.profils.show', $profil) }}" class="card border-0 shadow-sm h-100 text-reset text-decoration-none offre-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="avatar-initiales">{{ mb_strtoupper(mb_substr($profil->user->name, 0, 1)) }}</span>
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ $profil->titre_profil ?? 'Profil à compléter' }}</div>
                                <small class="text-muted">{{ $profil->user->name }}</small>
                            </div>
                        </div>
                        <div class="small text-muted mb-2">
                            {{ collect([$profil->ville, $profil->niveau_etude, $profil->annees_experience !== null ? $profil->annees_experience.' an(s) d\'exp.' : null])->filter()->join(' · ') }}
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach (array_slice($profil->listeCompetences(), 0, 4) as $c)
                                <span class="badge text-bg-light border">{{ $c }}</span>
                            @endforeach
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">Aucun profil ne correspond à ces critères.</div>
        @endforelse
    </div>
    <div class="mt-3">{{ $profils->links() }}</div>
@endsection

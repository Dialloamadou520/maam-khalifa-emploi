@props(['libelle', 'valeur', 'icone' => 'bar-chart', 'couleur' => 'primary', 'lien' => null])
<div class="card border-0 shadow-sm h-100 card-stat">
    <div class="card-body d-flex align-items-center gap-3">
        <span class="stat-icone bg-{{ $couleur }}-subtle text-{{ $couleur }}"><i class="bi bi-{{ $icone }}"></i></span>
        <div class="min-w-0">
            <div class="fs-4 fw-bold lh-1">{{ $valeur }}</div>
            <div class="small text-muted lh-sm mt-1">
                @if ($lien)
                    <a href="{{ $lien }}" class="stretched-link text-reset text-decoration-none">{{ $libelle }}</a>
                @else
                    {{ $libelle }}
                @endif
            </div>
        </div>
    </div>
</div>

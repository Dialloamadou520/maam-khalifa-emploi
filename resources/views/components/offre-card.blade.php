@props(['offre'])
<div class="card border-0 shadow-sm h-100 carte-offre">
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <span class="badge text-bg-{{ $offre->type_contrat->couleur() }}">{{ $offre->type_contrat->label() }}</span>
            @if ($offre->publiee_le)
                <small class="text-muted">{{ $offre->publiee_le->diffForHumans() }}</small>
            @endif
        </div>
        <h3 class="h6 fw-bold mb-1">
            <a href="{{ route('offres.show', $offre) }}" class="stretched-link text-reset text-decoration-none">{{ $offre->titre }}</a>
        </h3>
        <div class="small text-muted mb-3">{{ $offre->company->nom }}</div>
        <div class="mt-auto small d-flex flex-wrap gap-3 text-muted">
            <span><i class="bi bi-geo-alt"></i> {{ $offre->lieu }}</span>
            @if ($offre->salaireLibelle())
                <span><i class="bi bi-cash"></i> {{ $offre->salaireLibelle() }}</span>
            @endif
            @if ($offre->date_limite)
                <span><i class="bi bi-calendar-event"></i> Jusqu'au {{ $offre->date_limite->translatedFormat('d M Y') }}</span>
            @endif
        </div>
    </div>
</div>

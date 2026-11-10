@extends('layouts.espace')

@section('titre', 'Mes candidatures')
@section('sous_titre', 'Suivez l\'avancement de chacune de vos candidatures.')

@section('actions')
    <a href="{{ route('offres.index') }}" class="btn btn-primary"><i class="bi bi-search me-1"></i> Trouver une offre</a>
@endsection

@section('espace')
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('candidat.candidatures.index') }}" class="btn btn-sm {{ $statut ? 'btn-outline-secondary' : 'btn-primary' }}">Toutes</a>
        @foreach (\App\Enums\ApplicationStatus::cases() as $s)
            <a href="{{ route('candidat.candidatures.index', ['statut' => $s->value]) }}" class="btn btn-sm {{ $statut === $s->value ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $s->label() }}</a>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <ul class="list-group list-group-flush">
            @forelse ($candidatures as $candidature)
                <li class="list-group-item d-flex flex-wrap align-items-center gap-2 py-3">
                    <div class="min-w-0 flex-grow-1">
                        <a href="{{ route('offres.show', $candidature->jobOffer) }}" class="fw-semibold text-reset">{{ $candidature->jobOffer->titre }}</a>
                        <div class="small text-muted">{{ $candidature->jobOffer->company->nom }} · {{ $candidature->jobOffer->lieu }} · envoyée le {{ $candidature->created_at->translatedFormat('d M Y') }}</div>
                    </div>
                    <x-statut-badge :statut="$candidature->statut" />
                    @if ($candidature->statut === \App\Enums\ApplicationStatus::Envoyee)
                        <form method="POST" action="{{ route('candidat.candidatures.destroy', $candidature) }}" onsubmit="return confirm('Retirer cette candidature ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger">Retirer</button>
                        </form>
                    @endif
                </li>
            @empty
                <li class="list-group-item text-muted small py-4 text-center">Aucune candidature pour le moment.</li>
            @endforelse
        </ul>
    </div>

    <div class="mt-3">{{ $candidatures->links() }}</div>
@endsection

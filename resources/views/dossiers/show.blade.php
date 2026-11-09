@extends('layouts.espace')

@section('titre', 'Dossier '.$dossier->reference)
@section('sous_titre', $dossier->service->nom)

@section('actions')
    <a href="{{ route($prefixe.'.dossiers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Mes dossiers</a>
@endsection

@section('espace')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted fw-normal">Statut</dt>
                        <dd class="col-sm-8"><x-statut-badge :statut="$dossier->statut" /></dd>
                        <dt class="col-sm-4 text-muted fw-normal">Prestation</dt>
                        <dd class="col-sm-8">{{ $dossier->service->nom }}</dd>
                        @if ($dossier->scholarship)
                            <dt class="col-sm-4 text-muted fw-normal">Bourse</dt>
                            <dd class="col-sm-8"><a href="{{ route('bourses.show', $dossier->scholarship) }}">{{ $dossier->scholarship->titre }}</a></dd>
                        @endif
                        <dt class="col-sm-4 text-muted fw-normal">Ouvert le</dt>
                        <dd class="col-sm-8">{{ $dossier->created_at->translatedFormat('d F Y à H:i') }}</dd>
                        @if ($dossier->details)
                            <dt class="col-sm-4 text-muted fw-normal">Vos précisions</dt>
                            <dd class="col-sm-8 text-pre-line">{{ $dossier->details }}</dd>
                        @endif
                        @if ($dossier->note_admin)
                            <dt class="col-sm-4 text-muted fw-normal">Message de l'agence</dt>
                            <dd class="col-sm-8 text-pre-line">{{ $dossier->note_admin }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Montant</span>
                        <span class="fs-4 fw-bold">{{ fcfa($dossier->montant) }}</span>
                    </div>
                    @if ($dossier->estPaye())
                        <div class="alert alert-success small mb-3"><i class="bi bi-check-circle"></i> Prestation réglée.</div>
                    @elseif (Route::has($prefixe.'.paiements.create'))
                        <a href="{{ route($prefixe.'.paiements.create', $dossier) }}" class="btn btn-primary w-100 mb-3">Payer la prestation</a>
                    @else
                        <div class="alert alert-warning small mb-3">Paiement à effectuer auprès de l'agence.</div>
                    @endif

                    @if ($dossier->payments->isNotEmpty())
                        <div class="small fw-semibold mb-2">Paiements</div>
                        <ul class="list-group list-group-flush small">
                            @foreach ($dossier->payments as $paiement)
                                <li class="list-group-item px-0 d-flex justify-content-between">
                                    <span>{{ $paiement->reference }} · {{ $paiement->methode?->label() ?? '—' }}</span>
                                    <span><x-statut-badge :statut="$paiement->statut" /></span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.espace')

@section('titre', 'Payer la prestation')
@section('sous_titre', $dossier->service->nom.' · dossier '.$dossier->reference)

@section('espace')
    @if ($enAttente)
        <div class="alert alert-info small">Un paiement est déjà en cours de vérification par l'agence pour ce dossier.</div>
    @endif
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Montant à régler</span>
                        <span class="fs-3 fw-bold">{{ fcfa($reste) }}</span>
                    </div>
                    <h2 class="h6 fw-bold">Comment payer ?</h2>
                    <ol class="small ps-3 mb-0">
                        <li>Envoyez <strong>{{ fcfa($reste) }}</strong> à l'agence :
                            <ul class="mt-1">
                                <li>Wave : <strong>{{ config('agence.paiement.wave') }}</strong></li>
                                <li>Orange Money : <strong>{{ config('agence.paiement.orange_money') }}</strong></li>
                                <li>Free Money : <strong>{{ config('agence.paiement.free_money') }}</strong></li>
                                <li>Virement : <strong>{{ config('agence.paiement.virement') }}</strong></li>
                            </ul>
                        </li>
                        <li>Indiquez ci-contre la référence reçue par SMS.</li>
                        <li>L'agence confirme le paiement et lance votre prestation.</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <form method="POST" action="{{ route($prefixe.'.paiements.store', $dossier) }}" class="card border-0 shadow-sm" novalidate>
                @csrf
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3">Moyen de paiement</h2>
                    @error('methode')<div class="alert alert-danger small py-2">{{ $message }}</div>@enderror
                    <div class="row g-2 mb-3">
                        @foreach (\App\Enums\PaymentMethod::cases() as $m)
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="methode" id="methode-{{ $m->value }}" value="{{ $m->value }}" @checked(old('methode') === $m->value)>
                                <label class="btn btn-outline-primary w-100 h-100 small" for="methode-{{ $m->value }}">{{ $m->label() }}</label>
                            </div>
                        @endforeach
                    </div>
                    <label for="telephone" class="form-label">Numéro utilisé <small class="text-muted">(mobile money)</small></label>
                    <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', auth()->user()->telephone) }}" class="form-control mb-3 @error('telephone') is-invalid @enderror">
                    @error('telephone')<div class="invalid-feedback mb-3">{{ $message }}</div>@enderror
                    <label for="transaction_id" class="form-label">Référence de la transaction</label>
                    <input type="text" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" class="form-control @error('transaction_id') is-invalid @enderror" placeholder="Ex. TX-8F3K2…">
                    @error('transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted d-block mt-1">Inutile pour un paiement en espèces à l'agence.</small>
                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Déclarer mon paiement</button>
                        <a href="{{ route($prefixe.'.dossiers.show', $dossier) }}" class="btn btn-link">Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

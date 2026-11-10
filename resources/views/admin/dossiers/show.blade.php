@extends('layouts.espace')

@section('titre', 'Dossier '.$dossier->reference)
@section('sous_titre', $dossier->service->nom.' · '.$dossier->user->name)

@section('actions')
    <a href="{{ route('admin.dossiers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dossiers</a>
@endsection

@section('espace')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <dl class="row small mb-0">
                        <dt class="col-sm-4 text-muted fw-normal">Statut</dt><dd class="col-sm-8"><x-statut-badge :statut="$dossier->statut" /></dd>
                        <dt class="col-sm-4 text-muted fw-normal">Client</dt>
                        <dd class="col-sm-8"><a href="{{ route('admin.utilisateurs.show', $dossier->user) }}">{{ $dossier->user->name }}</a> ({{ $dossier->user->role->label() }})<br>{{ $dossier->user->email }} · {{ $dossier->user->telephone }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal">Prestation</dt><dd class="col-sm-8">{{ $dossier->service->nom }}</dd>
                        @if ($dossier->scholarship)<dt class="col-sm-4 text-muted fw-normal">Bourse</dt><dd class="col-sm-8">{{ $dossier->scholarship->titre }} — {{ $dossier->scholarship->organisme }}</dd>@endif
                        <dt class="col-sm-4 text-muted fw-normal">Montant</dt><dd class="col-sm-8 fw-semibold">{{ fcfa($dossier->montant) }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal">Ouvert le</dt><dd class="col-sm-8">{{ $dossier->created_at->translatedFormat('d F Y à H:i') }}</dd>
                        @if ($dossier->traitePar)<dt class="col-sm-4 text-muted fw-normal">Suivi par</dt><dd class="col-sm-8">{{ $dossier->traitePar->name }}</dd>@endif
                        @if ($dossier->details)<dt class="col-sm-4 text-muted fw-normal">Demande</dt><dd class="col-sm-8 text-pre-line">{{ $dossier->details }}</dd>@endif
                    </dl>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Paiements</div>
                <ul class="list-group list-group-flush">
                    @forelse ($dossier->payments as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center small">
                            <span>{{ $p->reference }} · {{ fcfa($p->montant) }} · {{ $p->methode?->label() ?? '—' }} @if ($p->payee_le)<span class="text-muted">· {{ $p->payee_le->translatedFormat('d M Y') }}</span>@endif</span>
                            <x-statut-badge :statut="$p->statut" />
                        </li>
                    @empty
                        <li class="list-group-item small text-muted">Aucun paiement enregistré.</li>
                    @endforelse
                </ul>
                @if (Route::has('admin.paiements.store'))
                    @include('admin.paiements._form', ['dossier' => $dossier])
                @endif
            </div>
            @if ($dossier->user->documents->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">Documents du client</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($dossier->user->documents as $document)
                            <li class="list-group-item d-flex justify-content-between align-items-center small">
                                <span><span class="badge text-bg-light border">{{ $document->type->label() }}</span> {{ $document->titre }}</span>
                                <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="col-lg-5">
            <form method="POST" action="{{ route('admin.dossiers.update', $dossier) }}" class="card border-0 shadow-sm" novalidate>
                @csrf @method('PATCH')
                <div class="card-body">
                    <h2 class="h6 fw-bold">Traitement</h2>
                    <label for="statut" class="form-label small">Statut</label>
                    <select id="statut" name="statut" class="form-select mb-3">
                        @foreach (\App\Enums\ServiceRequestStatus::cases() as $s)
                            <option value="{{ $s->value }}" @selected($dossier->statut === $s)>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    <label for="note_admin" class="form-label small">Message au client <small class="text-muted">(visible dans son espace)</small></label>
                    <textarea id="note_admin" name="note_admin" rows="4" class="form-control mb-3">{{ old('note_admin', $dossier->note_admin) }}</textarea>
                    <button class="btn btn-primary w-100">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

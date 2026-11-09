@extends('layouts.espace')

@section('titre', 'Nouvelle mise en relation')
@section('sous_titre', 'Proposez un candidat à une entreprise, pour une offre, un besoin ou de façon spontanée.')

@section('espace')
    <form method="POST" action="{{ route('admin.mises-en-relation.store') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        <div class="card-body p-4 row g-3">
            <div class="col-md-6">
                <label for="candidate_id" class="form-label">Candidat</label>
                <select id="candidate_id" name="candidate_id" class="form-select @error('candidate_id') is-invalid @enderror" required>
                    <option value="">—</option>
                    @foreach ($candidats as $c)
                        <option value="{{ $c->id }}" @selected((int) old('candidate_id', $choix['candidat'] ?? 0) === $c->id)>{{ $c->name }} ({{ $c->email }})</option>
                    @endforeach
                </select>
                @error('candidate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="company_id" class="form-label">Entreprise <small class="text-muted">(déduite de l'offre ou du besoin)</small></label>
                <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                    <option value="">—</option>
                    @foreach ($entreprises as $e)
                        <option value="{{ $e->id }}" @selected((int) old('company_id', $choix['entreprise'] ?? 0) === $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </select>
                @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="job_offer_id" class="form-label">Offre <small class="text-muted">(facultatif)</small></label>
                <select id="job_offer_id" name="job_offer_id" class="form-select">
                    <option value="">—</option>
                    @foreach ($offres as $o)
                        <option value="{{ $o->id }}" @selected((int) old('job_offer_id', $choix['offre'] ?? 0) === $o->id)>{{ $o->titre }} — {{ $o->company->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="recruitment_request_id" class="form-label">Besoin en recrutement <small class="text-muted">(facultatif)</small></label>
                <select id="recruitment_request_id" name="recruitment_request_id" class="form-select">
                    <option value="">—</option>
                    @foreach ($besoins as $b)
                        <option value="{{ $b->id }}" @selected((int) old('recruitment_request_id', $choix['besoin'] ?? 0) === $b->id)>{{ $b->intitule_poste }} — {{ $b->company->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label for="commentaire" class="form-label">Commentaire</label>
                <textarea id="commentaire" name="commentaire" rows="3" class="form-control">{{ old('commentaire') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('admin.mises-en-relation.index') }}" class="btn btn-link">Annuler</a>
            </div>
        </div>
    </form>
@endsection

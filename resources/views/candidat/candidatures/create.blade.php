@extends('layouts.espace')

@section('titre', 'Postuler')
@section('sous_titre', $offre->titre.' — '.$offre->company->nom)

@section('espace')
    <form method="POST" action="{{ route('candidat.candidatures.store', $offre) }}" enctype="multipart/form-data" class="card border-0 shadow-sm" novalidate>
        @csrf
        <div class="card-body p-4">
            <h2 class="h6 fw-bold">CV <span class="text-danger">*</span></h2>
            @if ($cvs->isNotEmpty())
                <select name="cv_document_id" class="form-select mb-2 @error('cv_document_id') is-invalid @enderror" aria-label="CV">
                    <option value="">— Déposer un nouveau CV ci-dessous —</option>
                    @foreach ($cvs as $cv)
                        <option value="{{ $cv->id }}" @selected((int) old('cv_document_id', $cvs->first()->id) === $cv->id)>{{ $cv->titre }} ({{ $cv->created_at->translatedFormat('d M Y') }})</option>
                    @endforeach
                </select>
            @endif
            <input type="file" name="cv_fichier" class="form-control @error('cv_fichier') is-invalid @enderror @error('cv_document_id') is-invalid @enderror" accept=".pdf,.doc,.docx" aria-label="Nouveau CV">
            @error('cv_fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @error('cv_document_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <h2 class="h6 fw-bold mt-4">Lettre de motivation <small class="text-muted fw-normal">(facultative)</small></h2>
            @if ($lettres->isNotEmpty())
                <select name="lettre_document_id" class="form-select mb-2" aria-label="Lettre de motivation">
                    <option value="">— Aucune / nouvelle lettre —</option>
                    @foreach ($lettres as $lettre)
                        <option value="{{ $lettre->id }}" @selected((int) old('lettre_document_id') === $lettre->id)>{{ $lettre->titre }}</option>
                    @endforeach
                </select>
            @endif
            <input type="file" name="lettre_fichier" class="form-control @error('lettre_fichier') is-invalid @enderror" accept=".pdf,.doc,.docx" aria-label="Nouvelle lettre">
            @error('lettre_fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <label for="message" class="form-label h6 fw-bold mt-4">Message au recruteur <small class="text-muted fw-normal">(facultatif)</small></label>
            <textarea id="message" name="message" rows="4" class="form-control">{{ old('message') }}</textarea>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Envoyer ma candidature</button>
                <a href="{{ route('offres.show', $offre) }}" class="btn btn-link">Annuler</a>
            </div>
        </div>
    </form>
@endsection

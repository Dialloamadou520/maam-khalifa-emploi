@extends('layouts.espace')

@section('titre', 'CV & lettres de motivation')
@section('sous_titre', 'Déposez vos documents une fois, réutilisez-les pour chaque candidature.')

@section('espace')
    <form method="POST" action="{{ route('candidat.documents.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm mb-4" novalidate>
        @csrf
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-3">
                <label for="type" class="form-label">Type</label>
                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                    @foreach (\App\Enums\DocumentType::options() as $valeur => $libelle)
                        <option value="{{ $valeur }}" @selected(old('type') === $valeur)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="titre" class="form-label">Intitulé <small class="text-muted">(facultatif)</small></label>
                <input type="text" id="titre" name="titre" value="{{ old('titre') }}" class="form-control" placeholder="CV 2026">
            </div>
            <div class="col-md-4">
                <label for="fichier" class="form-label">Fichier</label>
                <input type="file" id="fichier" name="fichier" class="form-control @error('fichier') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                @error('fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Déposer</button>
            </div>
            <div class="col-12"><small class="text-muted">PDF, Word ou image — 5 Mo maximum.</small></div>
        </div>
    </form>

    @foreach (\App\Enums\DocumentType::cases() as $type)
        @php($liste = $documents->get($type->value, collect()))
        @continue($liste->isEmpty() && ! in_array($type, [\App\Enums\DocumentType::Cv, \App\Enums\DocumentType::LettreMotivation]))
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">{{ $type->label() }} <span class="badge text-bg-light">{{ $liste->count() }}</span></div>
            <ul class="list-group list-group-flush">
                @forelse ($liste as $document)
                    <li class="list-group-item d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text fs-5 text-primary"></i>
                        <div class="min-w-0 flex-grow-1">
                            <div class="fw-semibold text-truncate">{{ $document->titre }}</div>
                            <small class="text-muted">{{ $document->nom_original }} · {{ number_format($document->taille / 1024, 0, ',', ' ') }} Ko · {{ $document->created_at->translatedFormat('d M Y') }}</small>
                        </div>
                        <a href="{{ route('candidat.documents.show', $document) }}" class="btn btn-sm btn-outline-secondary" title="Télécharger"><i class="bi bi-download"></i></a>
                        <form method="POST" action="{{ route('candidat.documents.destroy', $document) }}" onsubmit="return confirm('Supprimer ce document ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item small text-muted">Aucun document de ce type.</li>
                @endforelse
            </ul>
        </div>
    @endforeach
@endsection

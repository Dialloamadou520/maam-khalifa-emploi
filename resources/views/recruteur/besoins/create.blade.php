@extends('layouts.espace')

@section('titre', 'Nouveau besoin en recrutement')
@section('sous_titre', 'Plus votre besoin est précis, plus la présélection sera pertinente.')

@section('espace')
    <form method="POST" action="{{ route('recruteur.besoins.store') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        <div class="card-body p-4 row g-3">
            <div class="col-md-8">
                <label for="intitule_poste" class="form-label">Intitulé du poste</label>
                <input type="text" id="intitule_poste" name="intitule_poste" value="{{ old('intitule_poste') }}" class="form-control @error('intitule_poste') is-invalid @enderror" required>
                @error('intitule_poste')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="nombre_postes" class="form-label">Nombre de postes</label>
                <input type="number" min="1" id="nombre_postes" name="nombre_postes" value="{{ old('nombre_postes', 1) }}" class="form-control @error('nombre_postes') is-invalid @enderror" required>
                @error('nombre_postes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="type_contrat" class="form-label">Type de contrat</label>
                <select id="type_contrat" name="type_contrat" class="form-select @error('type_contrat') is-invalid @enderror" required>
                    <option value="">—</option>
                    @foreach (\App\Enums\ContractType::options() as $v => $l)
                        <option value="{{ $v }}" @selected(old('type_contrat') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                @error('type_contrat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="lieu" class="form-label">Lieu</label>
                <input type="text" id="lieu" name="lieu" value="{{ old('lieu') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="date_souhaitee" class="form-label">Prise de poste souhaitée</label>
                <input type="date" id="date_souhaitee" name="date_souhaitee" value="{{ old('date_souhaitee') }}" class="form-control @error('date_souhaitee') is-invalid @enderror">
                @error('date_souhaitee')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Missions et contexte</label>
                <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8">
                <label for="competences_requises" class="form-label">Compétences requises</label>
                <textarea id="competences_requises" name="competences_requises" rows="3" class="form-control">{{ old('competences_requises') }}</textarea>
            </div>
            <div class="col-md-4">
                <label for="budget" class="form-label">Budget salarial mensuel (FCFA)</label>
                <input type="number" min="0" step="5000" id="budget" name="budget" value="{{ old('budget') }}" class="form-control @error('budget') is-invalid @enderror">
                @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Transmettre à l'agence</button></div>
        </div>
    </form>
@endsection

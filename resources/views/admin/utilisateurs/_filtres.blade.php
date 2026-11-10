<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body row g-2 align-items-end">
        <div class="col-md-6">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control form-control-sm" placeholder="Nom, email, téléphone{{ $avecEntreprise ? ', entreprise' : '' }}…">
        </div>
        <div class="col-md-4">
            <label for="etat" class="form-label small">État</label>
            <select id="etat" name="etat" class="form-select form-select-sm">
                <option value="">Tous</option>
                <option value="actifs" @selected(($filtres['etat'] ?? null) === 'actifs')>Actifs</option>
                <option value="inactifs" @selected(($filtres['etat'] ?? null) === 'inactifs')>Désactivés</option>
                @if ($avecEntreprise)
                    <option value="a_verifier" @selected(($filtres['etat'] ?? null) === 'a_verifier')>Entreprise à vérifier</option>
                @endif
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-sm btn-primary">Filtrer</button></div>
    </div>
</form>

<form method="POST" action="{{ route('admin.paiements.store', $dossier) }}" class="card-body border-top" novalidate>
    @csrf
    <div class="small fw-semibold mb-2">Enregistrer un encaissement</div>
    <div class="row g-2">
        <div class="col-md-4">
            <input type="number" min="1" name="montant" value="{{ old('montant', max(0, $dossier->montant - $dossier->payments->where('statut', \App\Enums\PaymentStatus::Payee)->sum('montant'))) }}" class="form-control form-control-sm @error('montant') is-invalid @enderror" aria-label="Montant">
        </div>
        <div class="col-md-4">
            <select name="methode" class="form-select form-select-sm" aria-label="Moyen de paiement">
                @foreach (\App\Enums\PaymentMethod::options() as $v => $l)
                    <option value="{{ $v }}">{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="transaction_id" class="form-control form-control-sm" placeholder="Référence" aria-label="Référence de transaction">
        </div>
        <div class="col-12"><button class="btn btn-sm btn-success">Encaisser</button></div>
    </div>
</form>

@extends('layouts.espace')

@section('titre', 'Nouvelle demande')
@section('sous_titre', 'Choisissez la prestation souhaitée : l\'agence ouvre un dossier et vous accompagne.')

@section('espace')
    <form method="POST" action="{{ route($prefixe.'.dossiers.store') }}" class="card border-0 shadow-sm" novalidate>
        @csrf
        <div class="card-body p-4">
            <h2 class="h6 fw-bold mb-3">Prestation</h2>
            @error('service_id')<div class="alert alert-danger small py-2">{{ $message }}</div>@enderror
            <div class="row g-2 mb-4">
                @forelse ($services as $service)
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="service_id" id="service-{{ $service->id }}" value="{{ $service->id }}" @checked((int) old('service_id', $serviceChoisi) === $service->id)>
                        <label class="btn btn-outline-secondary w-100 h-100 text-start p-3" for="service-{{ $service->id }}">
                            <span class="d-flex justify-content-between gap-2">
                                <span class="fw-semibold">{{ $service->nom }}</span>
                                <span class="fw-bold text-nowrap">{{ fcfa($service->prix) }}</span>
                            </span>
                            <small class="d-block text-muted">{{ $service->description }}</small>
                        </label>
                    </div>
                @empty
                    <div class="col-12 text-muted small">Aucune prestation disponible pour le moment.</div>
                @endforelse
            </div>

            @if ($bourses->isNotEmpty())
                <label for="scholarship_id" class="form-label h6 fw-bold">Bourse ou financement concerné <small class="text-muted fw-normal">(facultatif)</small></label>
                <select id="scholarship_id" name="scholarship_id" class="form-select mb-4">
                    <option value="">—</option>
                    @foreach ($bourses as $bourse)
                        <option value="{{ $bourse->id }}" @selected((int) old('scholarship_id', $bourseChoisie) === $bourse->id)>{{ $bourse->titre }} — {{ $bourse->organisme }}</option>
                    @endforeach
                </select>
            @endif

            <label for="details" class="form-label h6 fw-bold">Précisions pour l'agence</label>
            <textarea id="details" name="details" rows="4" class="form-control" placeholder="Votre besoin, vos disponibilités, toute information utile…">{{ old('details') }}</textarea>

            <button type="submit" class="btn btn-primary mt-4">Envoyer ma demande</button>
        </div>
    </form>
@endsection

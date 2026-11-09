@extends('layouts.guest')

@section('titre', 'Créer un compte')
@section('largeur', '7')
@section('titre_carte', 'Créer votre compte')
@section('sous_titre', 'Inscription gratuite. Choisissez le profil qui vous correspond.')

@section('formulaire')
    @php($role = old('role', $profil))

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="row g-2 mb-4 choix-profil">
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="role-candidat" value="candidat" @checked($role === 'candidat')>
                <label class="btn btn-outline-secondary w-100 h-100 text-start p-3" for="role-candidat">
                    <i class="bi bi-person-badge fs-4 d-block mb-1"></i>
                    <span class="fw-semibold d-block">Je suis candidat</span>
                    <small class="text-muted">Je cherche un emploi, une bourse ou un accompagnement.</small>
                </label>
            </div>
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="role-recruteur" value="recruteur" @checked($role === 'recruteur')>
                <label class="btn btn-outline-secondary w-100 h-100 text-start p-3" for="role-recruteur">
                    <i class="bi bi-building fs-4 d-block mb-1"></i>
                    <span class="fw-semibold d-block">Je recrute</span>
                    <small class="text-muted">Je représente une entreprise et je recrute.</small>
                </label>
            </div>
            @error('role')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3" id="champ-entreprise" @if($role !== 'recruteur') hidden @endif>
            <label for="entreprise" class="form-label">Nom de l'entreprise</label>
            <input type="text" class="form-control @error('entreprise') is-invalid @enderror" id="entreprise"
                   name="entreprise" value="{{ old('entreprise') }}" autocomplete="organization">
            @error('entreprise')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="name" class="form-label" id="label-nom">{{ $role === 'recruteur' ? 'Nom du contact' : 'Nom complet' }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                   name="name" value="{{ old('name') }}" required autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                       name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone"
                       name="telephone" value="{{ old('telephone') }}" required autocomplete="tel" placeholder="77 000 00 00">
                @error('telephone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                       name="password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirmation</label>
                <input type="password" class="form-control" id="password_confirmation"
                       name="password_confirmation" required autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Créer mon compte</button>
    </form>

    <p class="text-muted small mt-3 mb-0">
        Déjà inscrit ? <a href="{{ route('login') }}">Connectez-vous</a>.
    </p>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('input[name="role"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                var recruteur = this.value === 'recruteur';
                document.getElementById('champ-entreprise').hidden = !recruteur;
                document.getElementById('label-nom').textContent = recruteur ? 'Nom du contact' : 'Nom complet';
            });
        });
    </script>
@endpush

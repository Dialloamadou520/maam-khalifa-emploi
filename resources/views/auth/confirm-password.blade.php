@extends('layouts.guest')

@section('titre', 'Confirmation du mot de passe')
@section('titre_carte', 'Confirmation requise')
@section('sous_titre', 'Merci de confirmer votre mot de passe avant de continuer.')

@section('formulaire')
    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                   name="password" required autofocus autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Confirmer</button>
    </form>
@endsection

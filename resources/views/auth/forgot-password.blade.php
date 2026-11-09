@extends('layouts.guest')

@section('titre', 'Mot de passe oublié')
@section('titre_carte', 'Mot de passe oublié')
@section('sous_titre', 'Nous vous enverrons un lien de réinitialisation par email.')

@section('formulaire')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                   name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
    </form>
@endsection

@extends('layouts.guest')

@section('titre', 'Vérification de l\'email')
@section('titre_carte', 'Vérifiez votre adresse email')
@section('sous_titre', "Un lien de vérification vient de vous être envoyé par email.")

@section('formulaire')
    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success">Un nouveau lien de vérification vous a été envoyé.</div>
    @endif

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Renvoyer le lien</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Se déconnecter</button>
        </form>
    </div>
@endsection

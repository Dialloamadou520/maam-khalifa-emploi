@extends('layouts.base')

@section('contenu')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-@yield('largeur', '6')">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold mb-1">@yield('titre_carte')</h1>
                    <p class="text-muted mb-4">@yield('sous_titre')</p>
                    @yield('formulaire')
                </div>
            </div>
        </div>
    </div>
@endsection

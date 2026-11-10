<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Accueil') — {{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Agence Maam Khalifa : emploi, recrutement et accompagnement professionnel au Sénégal.')">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary">
    @include('layouts.partials.navbar')

    @hasSection('hero')
        @yield('hero')
    @endif

    <main class="flex-grow-1 py-4">
        <div class="container">
            @sectionMissing('espace')
                @include('layouts.partials.alerts')
            @endif
            @yield('contenu')
        </div>
    </main>

    @include('layouts.partials.footer')

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>

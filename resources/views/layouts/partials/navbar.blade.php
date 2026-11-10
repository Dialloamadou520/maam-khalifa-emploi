<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('accueil') }}">
            <span class="logo-mk">MK</span>
            <span class="lh-1">Maam Khalifa<small class="d-block fw-normal opacity-75 logo-sous-titre">Emploi &amp; recrutement</small></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPrincipal"
                aria-controls="navbarPrincipal" aria-expanded="false" aria-label="Ouvrir la navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('offres.*')) active @endif" href="{{ route('offres.index') }}">Offres d'emploi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('bourses.*')) active @endif" href="{{ route('bourses.index') }}">Bourses &amp; financements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('prestations')) active @endif" href="{{ route('prestations') }}">Prestations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('a-propos')) active @endif" href="{{ route('a-propos') }}">L'agence</a>
                </li>
            </ul>
            <ul class="navbar-nav align-items-lg-center">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-accent btn-sm fw-semibold ms-lg-2 mt-2 mt-lg-0" href="{{ route('register') }}">
                            Créer un compte
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i> Mon espace</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-1"></i> Se déconnecter</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

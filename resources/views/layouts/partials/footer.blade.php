<footer class="footer-mk text-white-50 pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="fw-bold text-white fs-5 mb-2">Agence Maam Khalifa</div>
                <p class="small mb-0">Emploi, recrutement et accompagnement professionnel : nous rapprochons les talents et les entreprises.</p>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-white fw-semibold mb-2">Candidats</div>
                <ul class="list-unstyled small mb-0">
                    <li><a href="{{ route('offres.index') }}">Offres d'emploi</a></li>
                    <li><a href="{{ route('bourses.index') }}">Bourses &amp; financements</a></li>
                    <li><a href="{{ route('register') }}">Créer mon profil</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <div class="text-white fw-semibold mb-2">Entreprises</div>
                <ul class="list-unstyled small mb-0">
                    <li><a href="{{ route('register', ['profil' => 'recruteur']) }}">Publier une offre</a></li>
                    <li><a href="{{ route('prestations') }}">Nos prestations</a></li>
                    <li><a href="{{ route('a-propos') }}">Contacter l'agence</a></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="small d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} Agence Maam Khalifa — Tous droits réservés</span>
            <span>Dakar, Sénégal</span>
        </div>
    </div>
</footer>

# Agence Maam Khalifa — Plateforme emploi & recrutement

Plateforme web de gestion de l'emploi, du recrutement et de l'accompagnement
professionnel de l'Agence Maam Khalifa.

- **Espace candidat** : profil professionnel, CV et lettres de motivation, offres
  d'emploi, bourses et financements, suivi des dossiers, paiement des prestations.
- **Espace recruteur** : compte entreprise, publication d'offres, besoins en
  recrutement, candidatures reçues, recherche de profils.
- **Espace administrateur** : tableau de bord, gestion des comptes, validation des
  offres, dossiers, prestations et tarifs, paiements, mises en relation,
  statistiques.

## Stack

PHP 8.3, Laravel 13, Blade + Bootstrap 5 (fichiers servis depuis `public/vendor`,
aucune compilation front), MySQL / PostgreSQL (Supabase), déploiement Vercel
(`vercel-php`).

## Démarrage local

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Tests : `php artisan test` — style : `./vendor/bin/pint --test`.

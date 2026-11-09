# Déploiement

Même principe que pour UNI-INSCRIPT : Laravel tourne sur Vercel grâce au runtime
communautaire `vercel-php` (`vercel.json` + `api/index.php`), avec une base
PostgreSQL hébergée chez Supabase. Un hébergeur PHP classique (Railway, Render,
Hostinger…) fonctionne aussi, avec la racine du site sur `public/`.

## 1. Base de données (Supabase)

*Project Settings → Database → Connection string* : utilisez le **Session
pooler** (port 5432). Le pooler « Transaction » (6543) ne supporte pas les
requêtes préparées de PDO.

## 2. Variables Vercel (Production)

```env
APP_KEY=base64:...            # php artisan key:generate --show
APP_URL=https://<projet>.vercel.app

DB_CONNECTION=pgsql
DB_HOST=aws-0-<region>.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.<reference-du-projet>
DB_PASSWORD=<mot de passe>
DB_SSLMODE=require

AGENCE_EMAIL=contact@maamkhalifa.sn
AGENCE_TELEPHONE="+221 77 000 00 00"
```

Ne recopiez pas tout `.env.example` : une variable vide écrase la valeur par
défaut de Laravel et provoque une erreur 500.

## 3. Schéma et données initiales

Artisan ne peut pas être lancé sur Vercel : depuis votre machine, avec les mêmes
valeurs `DB_*` dans `.env` :

```bash
php artisan migrate --force
php artisan db:seed --force    # administrateur + prestations/tarifs de départ
```

Compte créé : `admin@maamkhalifa.sn` / `password` — **changez le mot de passe
immédiatement**. En production, les données de démonstration (`DemoSeeder`) ne
sont pas insérées.

## Limites de Vercel

Pas de disque persistant (seul `/tmp` est inscriptible) : les CV et lettres de
motivation devront être stockés sur un stockage externe (Supabase Storage, S3).

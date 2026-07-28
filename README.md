# EvoPro

Système de gestion EvoPro (Laravel 12) — clients, projets, paiements, tableau de bord.

## URLs

| Environnement | URL |
|---------------|-----|
| Local | http://127.0.0.1:8002 |
| Production | https://evopro.a2spr.com |

## Local

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve --port=8002
```

## Déploiement (Render + a2spr.com)

Le code est sur GitHub : https://github.com/a2spro2026/evopro

**Déploiement en 1 clic (Render Blueprint) :**

https://render.com/deploy?repo=https://github.com/a2spro2026/evopro

1. Ouvrir le lien ci-dessus et connecter le compte Render (ou GitHub).
2. Créer le service **evopro** (le fichier `render.yaml` configure PHP, SQLite, sessions fichier).
3. Dans Render → service **evopro** → **Settings** → **Custom Domains**, ajouter : `evopro.a2spr.com`
4. Attendre le déploiement (build ~2–5 min).

Le DNS `evopro.a2spr.com` pointe déjà vers l’infra A2SPR ; une fois le domaine ajouté sur Render, l’app sera accessible en production.

## Stack

- Laravel 12, PHP 8.2+
- Données métier en session (clients, projets, paiements)
- SQLite (migrations Laravel par défaut)
- Chart.js (CDN) pour le tableau de bord

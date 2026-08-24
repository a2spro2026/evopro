# EvoPro

Système de gestion EvoPro (Laravel 12).

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

## Archive contacts

Les numéros et noms exportés sont conservés dans **`contacts-archive/`** (dossier indépendant de `storage/app/evopro_data.json`).

```bash
php artisan tinker --execute="App\Support\ContactsArchive::export();"
```

## État actuel

L’application a été remise à zéro : connexion + tableau de bord minimal. Les anciennes sections (Client, Projets, Relance, etc.) ont été retirées pour reconstruire le projet proprement.

## Déploiement VPS

```bash
deploy-evopro.bat
```

(GitHub push seul ne met pas à jour la production.)

## Stack

- Laravel 12, PHP 8.2+
- Données métier : `storage/app/evopro_data.json` via `AppStore`
- SQLite (migrations Laravel par défaut)

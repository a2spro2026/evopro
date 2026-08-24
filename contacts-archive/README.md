# Archive contacts EvoPro

Dossier **indépendant** des données applicatives (`storage/app/evopro_data.json`).

Les numéros et noms exportés depuis Relance, Fiche Client et WhatsApp sont conservés ici avant chaque remise à zéro de l’application.

## Fichiers

| Fichier | Description |
|---------|-------------|
| `contacts.json` | Liste structurée (nom, téléphone, source, ref, ville) |
| `contacts.csv` | Même contenu, séparateur `;` (Excel / LibreOffice) |

## Ré-exporter

Depuis la racine du projet :

```bash
php artisan tinker --execute="App\Support\ContactsArchive::export();"
```

Ou après restauration temporaire de `evopro_data.json`, relancer la commande ci-dessus.

## Note

Ce dossier est versionné dans Git. Les données métier en cours restent dans `storage/app/evopro_data.json` (non versionné).

# CollectPlus Togo

Application web de gestion des déchets pour une commune du Grand Lomé : les habitants signalent les dépôts
sauvages et demandent des collectes, la mairie planifie les tournées, les collecteurs les réalisent sur le terrain
avec leur téléphone.

## Ce que fait l'application

**Habitants**
- Signaler un dépôt de déchets en le plaçant sur la carte, avec photo ; suivre son traitement.
- Demander une collecte (encombrants, déchets verts, déménagement…) et recevoir la date de passage.
- Déposer une plainte et lire la réponse de la mairie.
- Consulter le calendrier de collecte du quartier ; être prévenu la veille de chaque passage.

**Collecteurs** (sur téléphone)
- Feuille de route de la tournée : carte, étapes dans l'ordre, navigation GPS vers chaque point.
- Valider chaque passage avec une photo, la quantité et la position GPS, ou indiquer pourquoi un point n'a pas pu être collecté.
- Signaler un incident (panne, rue bloquée…) et recevoir la réponse de l'administration.

**Administration**
- Traiter les signalements, demandes de collecte, plaintes et incidents, avec notification automatique de l'intéressé.
- Gérer les points de collecte et composer les tournées ; suivre leur avancement étape par étape.
- Calendrier des passages par quartier.
- Carte de la commune, rapport d'activité imprimable et exports Excel.
- Comptes des agents et collecteurs (mot de passe provisoire à changer à la première connexion).

## Technique

Laravel 12 · PHP 8.2 · MySQL · Bootstrap 5 · Leaflet + OpenStreetMap (cartes sans clé d'API).
Les photos sont réduites dans le navigateur avant l'envoi pour les connexions mobiles lentes.

## Installation en local (XAMPP)

```bash
cd Gestions_Dechets
composer install
cp .env.example .env              # puis : APP_ENV=local, APP_DEBUG=true, MAIL_MAILER=log, SESSION_SECURE_COOKIE=false, DB_*
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed --class=AdminUserSeeder   # comptes de démonstration
php artisan db:seed --class=CollecteSeeder    # points de collecte et une tournée de démonstration
php artisan serve                              # http://127.0.0.1:8000
```

Comptes de démonstration (mot de passe `password`) : `admin@lome.tg`, `collecteur@test.com`, `citoyen@test.com`.
À ne jamais créer sur un serveur de production.

En local, les e-mails (mot de passe oublié) sont écrits dans `storage/logs/laravel.log`.
Les rappels de collecte se déclenchent à la main avec `php artisan collectes:rappeler`.

## Tests

```bash
php artisan test
```

Les tests rejouent les parcours complets (tournée d'un collecteur, traitement d'une demande, mot de passe oublié…)
sur une base SQLite en mémoire : la base MySQL n'est pas touchée. `ExplorationTest` ouvre toutes les pages des
trois espaces avec un jeu de données complet : à lancer après chaque modification.

## Documentation

- [Mise en production](docs/deploiement.md) : serveur, `.env`, tâche planifiée, sauvegardes, liste de contrôle.
- [Connexion avec Google](docs/google-oauth.md)
- [Diagrammes UML](docs/diagrammes/) : cas d'utilisation, séquences, activités.

## Organisation du code

| Dossier | Contenu |
|---|---|
| `app/Http/Controllers` | Un contrôleur par espace : `Citoyen…`, `Collecteur…`, `Admin…` (un par module d'administration) |
| `app/Models` | Signalement, DemandeCollecte, Plainte, PointDeCollecte, Itineraire (tournée), Collecte (passage), Incident… |
| `app/Console/Commands` | `collectes:rappeler` (rappels de la veille), `assets:telecharger` |
| `resources/views/components` | Cartes (`carte/choix`, `carte/apercu`, `carte/points`), avatar |
| `public/js` | `carte.js` (Leaflet), `photos.js` (réduction des photos avant envoi) |
| `config/quartiers.php` | Liste des quartiers proposés à la saisie |
| `config/collectplus.php` | Informations de l'organisme exploitant (pages légales) |

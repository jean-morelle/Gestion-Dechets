# Mise en production

Liste de contrôle pour installer CollectPlus Togo sur le serveur de la commune.

## Serveur

- PHP 8.2 ou plus, avec les extensions `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `xml`.
- MySQL 8 (ou MariaDB 10.6) avec un compte dédié, pas `root`.
- HTTPS obligatoire (certificat Let's Encrypt par exemple).
- **La racine web doit pointer sur `public/`**, jamais sur le dossier du projet. Exemple Apache : [`apache-vhost.conf`](apache-vhost.conf).

## Installation

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env          # puis renseigner le fichier (voir ci-dessous)
php artisan key:generate
php artisan migrate --force
php artisan storage:link      # photos des signalements et des passages
php artisan assets:telecharger   # facultatif : Bootstrap, Leaflet… servis en local
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Créer ensuite le premier administrateur :

```bash
php artisan tinker
>>> App\Models\User::create(['name' => '…', 'email' => '…', 'password' => bcrypt('…'), 'role' => 'admin', 'statut' => 'actif']);
```

Les autres comptes (agents, collecteurs) se créent depuis l'application : *Utilisateurs → Créer un compte agent*.

## Fichier `.env`

| Clé | À vérifier |
|---|---|
| `APP_ENV=production`, `APP_DEBUG=false` | Le mode debug affiche le code et les secrets en cas d'erreur. |
| `APP_URL` | Adresse publique en `https://`. |
| `DB_*` | Compte MySQL dédié. |
| `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true` | Cookies de session protégés. |
| `MAIL_*` | Serveur d'envoi (mot de passe oublié). Sans lui, aucun e-mail ne part. |
| `EXPLOITANT_*`, `HEBERGEUR`, `DECLARATION_IPDCP` | Affichés dans la politique de confidentialité (sinon : « [à compléter] »). |
| `GOOGLE_*` | Facultatif, voir [`google-oauth.md`](google-oauth.md). |

## Tâche planifiée

Les rappels de collecte (chaque soir à 18 h) ont besoin du planificateur Laravel :

```cron
* * * * * cd /chemin/vers/Gestions_Dechets && php artisan schedule:run >> /dev/null 2>&1
```

Vérifier avec `php artisan schedule:list`.

## Sauvegardes

À mettre en place par l'hébergeur, au minimum chaque nuit :

- la base MySQL (`mysqldump`) ;
- le dossier `storage/app/public` (photos) ;
- le fichier `.env`, conservé à part et en lieu sûr.

Tester une restauration avant la mise en service.

## Avant l'ouverture au public

- [ ] `php artisan test` : tous les tests passent.
- [ ] Le fichier `https://<domaine>/.env` renvoie une erreur 403 ou 404.
- [ ] « Mot de passe oublié » : l'e-mail arrive bien.
- [ ] Pages Confidentialité et Conditions relues par le service juridique, sans « [à compléter] ».
- [ ] Déclaration du traitement auprès de l'IPDCP effectuée.
- [ ] Points de collecte et calendrier des quartiers saisis.

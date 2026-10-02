# Connexion avec Google (facultatif)

Le bouton « Continuer avec Google » permet aux habitants de se connecter sans créer de mot de passe.
Sans configuration, le bouton reste visible mais la connexion échoue avec un message d'erreur :
configurez-le ou retirez-le de `resources/views/auth/login.blade.php`.

## 1. Créer les identifiants

1. Ouvrir la [console Google Cloud](https://console.cloud.google.com/) et créer un projet.
2. *API et services → Écran de consentement OAuth* : type « Externe », nom de l'application, adresse de contact.
3. *Identifiants → Créer des identifiants → ID client OAuth*, type « Application Web ».
4. Ajouter l'URI de redirection autorisée :
   - en local : `http://127.0.0.1:8000/auth/google/callback`
   - en production : `https://<domaine>/auth/google/callback`

## 2. Renseigner le `.env`

```env
GOOGLE_CLIENT_ID=…
GOOGLE_CLIENT_SECRET=…
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

## Fonctionnement

- Adresse e-mail déjà inscrite : connexion directe (sauf compte suspendu ou désactivé).
- Nouvelle adresse : création d'un compte **citoyen**, avec un mot de passe aléatoire que l'utilisateur ne connaît pas
  (`mot_de_passe_defini = false`). Il peut en choisir un dans *Paramètres*, sans saisir d'ancien mot de passe.
- Les comptes collecteurs et administrateurs sont créés par l'administration, pas via Google.

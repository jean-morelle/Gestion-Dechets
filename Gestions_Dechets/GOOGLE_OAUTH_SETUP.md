# Configuration Google OAuth

## Étapes pour configurer l'authentification Google

### 1. Créer un projet Google Cloud Console

1. Allez sur [Google Cloud Console](https://console.cloud.google.com/)
2. Créez un nouveau projet ou sélectionnez un projet existant
3. Activez l'API Google+ (si disponible) ou Google Identity

### 2. Configurer les identifiants OAuth

1. Allez dans "APIs & Services" > "Credentials"
2. Cliquez sur "Create Credentials" > "OAuth client ID"
3. Sélectionnez "Web application"
4. Ajoutez les URLs autorisées :
   - **Authorized JavaScript origins**: `http://127.0.0.1:8000`
   - **Authorized redirect URIs**: `http://127.0.0.1:8000/auth/google/callback`

### 3. Configurer les variables d'environnement

Ajoutez ces variables à votre fichier `.env` :

```env
GOOGLE_CLIENT_ID=votre_client_id_google
GOOGLE_CLIENT_SECRET=votre_client_secret_google
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

### 4. Fonctionnalités implémentées

✅ **Package Laravel Socialite installé**
✅ **Contrôleur GoogleAuthController créé**
✅ **Routes OAuth configurées**
✅ **Migration pour google_id ajoutée**
✅ **Bouton Google sur la page de connexion**
✅ **Modèle User mis à jour**

### 5. Comment ça fonctionne

1. **Utilisateur clique** sur "Se connecter avec Google"
2. **Redirection** vers Google OAuth
3. **Utilisateur autorise** l'application
4. **Google redirige** vers `/auth/google/callback`
5. **Application vérifie** si l'utilisateur existe
6. **Si nouveau** : Crée un compte avec rôle "citoyen"
7. **Si existant** : Se connecte directement
8. **Redirection** vers le dashboard selon le rôle

### 6. Sécurité

- L'email Google est vérifié automatiquement
- Rôle par défaut : "citoyen"
- Mot de passe aléatoire généré
- Statut "actif" par défaut

### 7. Test

1. Configurez les variables d'environnement
2. Allez sur `/login`
3. Cliquez sur "Se connecter avec Google"
4. Autorisez l'application
5. Vous devriez être connecté automatiquement

## Notes importantes

- **Production** : Changez les URLs pour votre domaine
- **HTTPS** : Obligatoire en production
- **Permissions** : Configurez les scopes selon vos besoins




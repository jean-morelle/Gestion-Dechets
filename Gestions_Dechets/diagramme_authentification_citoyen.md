# Diagramme de Description Textuelle - Authentification Citoyen

## **Cas d'utilisation : Authentification**

### **Acteur :**
- **Acteur principal :** Citoyen
- **Acteurs secondaires :** Aucun

### **Pré-conditions :**
- Le citoyen possède un compte utilisateur valide OU souhaite créer un nouveau compte
- L'application est accessible et fonctionnelle
- Le citoyen a accès à un navigateur web ou une application mobile

### **Scénario nominal :**

1. **Accès à la page de connexion**
   - Le citoyen accède à l'URL de l'application
   - Il clique sur "Se connecter" ou est redirigé vers la page de connexion

2. **Saisie des identifiants**
   - Le citoyen saisit son adresse email dans le champ "Email"
   - Le citoyen saisit son mot de passe dans le champ "Mot de passe"
   - Le citoyen clique sur le bouton "Se connecter"

3. **Vérification des identifiants**
   - Le système vérifie l'existence de l'email dans la base de données
   - Le système vérifie que le mot de passe correspond
   - Le système vérifie que le compte est actif et que le rôle est "citoyen"

4. **Authentification réussie**
   - Le système crée une session utilisateur
   - Le système redirige le citoyen vers son tableau de bord
   - Le système affiche un message de bienvenue

### **Scénarios alternatifs :**

**2a. Inscription d'un nouveau citoyen**
- Le citoyen clique sur "Créer un compte"
- Il remplit le formulaire d'inscription (nom, email, mot de passe, téléphone, adresse)
- Il confirme son inscription par email
- Il peut ensuite se connecter

**3a. Email inexistant**
- Le système affiche "Email ou mot de passe incorrect"
- Le citoyen peut réessayer ou cliquer sur "Mot de passe oublié"

**3b. Mot de passe incorrect**
- Le système affiche "Email ou mot de passe incorrect"
- Le citoyen peut réessayer ou cliquer sur "Mot de passe oublié"

**3c. Compte désactivé**
- Le système affiche "Votre compte est désactivé. Contactez l'administrateur."
- Le citoyen ne peut pas se connecter

**3d. Rôle incorrect**
- Le système affiche "Accès refusé. Vous n'êtes pas autorisé à accéder à cette section."
- Le citoyen est redirigé vers la page de connexion

**4a. Connexion Google**
- Le citoyen clique sur "Se connecter avec Google"
- Il est redirigé vers Google pour l'authentification
- Après validation Google, il est connecté automatiquement

**4b. Mot de passe oublié**
- Le citoyen clique sur "Mot de passe oublié"
- Il saisit son email
- Il reçoit un email avec un lien de réinitialisation
- Il peut créer un nouveau mot de passe

### **Post-conditions :**

**En cas de succès :**
- Le citoyen est authentifié dans le système
- Une session utilisateur est créée
- Le citoyen accède à son tableau de bord personnel
- Le système enregistre la connexion dans les logs

**En cas d'échec :**
- Aucune session n'est créée
- Le citoyen reste sur la page de connexion
- Un message d'erreur approprié est affiché
- Les tentatives de connexion sont enregistrées pour la sécurité

### **Contraintes :**
- Les mots de passe doivent respecter les critères de sécurité
- Maximum 3 tentatives de connexion avant blocage temporaire
- Session automatique fermée après 30 minutes d'inactivité
- Connexion chiffrée HTTPS obligatoire

### **Données d'entrée :**
- Email du citoyen
- Mot de passe du citoyen

### **Données de sortie :**
- Session utilisateur authentifiée
- Redirection vers le tableau de bord citoyen
- Messages de confirmation ou d'erreur




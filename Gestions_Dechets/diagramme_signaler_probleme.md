# Diagramme de Description Textuelle - Signaler un Problème

## **Cas d'utilisation : Signaler un Problème**

### **Acteur :**
- **Acteur principal :** Citoyen
- **Acteurs secondaires :** Administrateur (réception du signalement)

### **Pré-conditions :**
- Le citoyen est authentifié dans le système
- Le citoyen a accès à l'interface de signalement
- Le citoyen a observé un problème lié aux déchets dans sa zone

### **Scénario nominal :**

1. **Accès au formulaire de signalement**
   - Le citoyen clique sur "Signaler un problème" dans son tableau de bord
   - Le système affiche le formulaire de signalement

2. **Saisie des informations du problème**
   - Le citoyen sélectionne le type de problème (déchets abandonnés, conteneur plein, déversement, etc.)
   - Le citoyen saisit une description détaillée du problème
   - Le citoyen indique la localisation (adresse ou géolocalisation automatique)
   - Le citoyen sélectionne le niveau d'urgence (faible, moyen, élevé)

3. **Ajout de preuves visuelles**
   - Le citoyen télécharge une ou plusieurs photos du problème
   - Le citoyen peut ajouter des notes complémentaires

4. **Soumission du signalement**
   - Le citoyen clique sur "Envoyer le signalement"
   - Le système valide les informations saisies
   - Le système enregistre le signalement avec un numéro de référence
   - Le système envoie une notification à l'administrateur

5. **Confirmation**
   - Le système affiche un message de confirmation
   - Le système attribue un numéro de suivi au signalement
   - Le citoyen reçoit un email de confirmation

### **Scénarios alternatifs :**

**2a. Géolocalisation automatique**
- Le citoyen autorise l'accès à sa position
- Le système remplit automatiquement l'adresse
- Le citoyen peut modifier si nécessaire

**3a. Problème pour télécharger des photos**
- Le système affiche un message d'erreur
- Le citoyen peut réessayer ou continuer sans photos
- Le signalement reste valide

**4a. Informations manquantes**
- Le système affiche les champs obligatoires non remplis
- Le citoyen complète les informations manquantes
- Il peut renvoyer le formulaire

**4b. Erreur de sauvegarde**
- Le système affiche "Erreur lors de l'envoi. Veuillez réessayer."
- Le citoyen peut renvoyer le signalement
- Les données saisies sont conservées temporairement

**5a. Signalement urgent**
- Si le niveau d'urgence est élevé, une notification SMS est envoyée
- L'administrateur reçoit une alerte prioritaire
- Le citoyen est informé du traitement accéléré

### **Post-conditions :**

**En cas de succès :**
- Le signalement est enregistré dans la base de données
- Un numéro de suivi est attribué
- L'administrateur est notifié
- Le citoyen peut suivre l'évolution dans "Mes Signalements"

**En cas d'échec :**
- Le signalement n'est pas enregistré
- Un message d'erreur est affiché
- Le citoyen peut réessayer

### **Contraintes :**
- Maximum 5 photos par signalement
- Taille maximale des photos : 5MB chacune
- Description obligatoire (minimum 20 caractères)
- Géolocalisation ou adresse obligatoire

### **Données d'entrée :**
- Type de problème
- Description du problème
- Localisation (adresse ou coordonnées GPS)
- Niveau d'urgence
- Photos (optionnelles)
- Notes complémentaires

### **Données de sortie :**
- Numéro de référence du signalement
- Statut du signalement
- Message de confirmation
- Email de confirmation

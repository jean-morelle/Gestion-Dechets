# Diagramme de Description Textuelle - Déposer une Plainte

## **Cas d'utilisation : Déposer une Plainte**

### **Acteur :**
- **Acteur principal :** Citoyen
- **Acteurs secondaires :** Administrateur (traitement de la plainte)

### **Pré-conditions :**
- Le citoyen est authentifié dans le système
- Le citoyen a un motif légitime de plainte
- Le citoyen a accès à l'interface de dépôt de plainte

### **Scénario nominal :**

1. **Accès au formulaire de plainte**
   - Le citoyen clique sur "Déposer une plainte" dans son tableau de bord
   - Le système affiche le formulaire de plainte

2. **Sélection du type de plainte**
   - Le citoyen sélectionne la catégorie (service de collecte, comportement d'un collecteur, infrastructure, etc.)
   - Le citoyen sélectionne la gravité (légère, modérée, grave)

3. **Saisie des détails de la plainte**
   - Le citoyen saisit un titre descriptif de sa plainte
   - Le citoyen décrit en détail les faits et circonstances
   - Le citoyen indique la date et l'heure de l'incident
   - Le citoyen précise la localisation de l'incident

4. **Ajout de preuves**
   - Le citoyen télécharge des photos ou documents justificatifs
   - Le citoyen peut joindre des témoignages ou preuves supplémentaires

5. **Demande de réparation**
   - Le citoyen précise ce qu'il souhaite comme réparation ou action
   - Le citoyen indique s'il souhaite rester anonyme

6. **Soumission de la plainte**
   - Le citoyen clique sur "Déposer la plainte"
   - Le système valide les informations saisies
   - Le système enregistre la plainte avec un numéro de référence
   - Le système envoie une notification à l'administrateur

7. **Confirmation**
   - Le système affiche un message de confirmation
   - Le citoyen reçoit un email de confirmation avec le numéro de suivi

### **Scénarios alternatifs :**

**2a. Plainte liée à un signalement existant**
- Le citoyen peut lier sa plainte à un signalement précédent
- Le système pré-remplit certaines informations
- Le citoyen complète les détails spécifiques à la plainte

**3a. Incident récurrent**
- Le citoyen peut indiquer si c'est un problème récurrent
- Le système suggère de vérifier les signalements précédents
- Le citoyen peut décider de créer une plainte groupée

**4a. Problème avec les fichiers**
- Le système vérifie la taille et le format des fichiers
- Si problème, affiche un message d'erreur avec les contraintes
- Le citoyen peut corriger et réessayer

**6a. Informations manquantes**
- Le système affiche les champs obligatoires non remplis
- Le citoyen complète les informations manquantes
- Il peut renvoyer le formulaire

**6b. Plainte en doublon**
- Le système détecte une plainte similaire récente
- Il propose de consulter la plainte existante
- Le citoyen peut confirmer ou modifier sa plainte

**7a. Plainte grave**
- Si la gravité est élevée, traitement prioritaire
- Notification immédiate à l'administrateur
- Le citoyen est informé du suivi accéléré

### **Post-conditions :**

**En cas de succès :**
- La plainte est enregistrée dans la base de données
- Un numéro de suivi est attribué
- L'administrateur est notifié pour traitement
- Le citoyen peut suivre l'évolution dans "Mes Plaintes"

**En cas d'échec :**
- La plainte n'est pas enregistrée
- Un message d'erreur est affiché
- Le citoyen peut réessayer

### **Contraintes :**
- Maximum 10 fichiers par plainte
- Taille maximale des fichiers : 10MB chacun
- Description obligatoire (minimum 50 caractères)
- Titre obligatoire (maximum 100 caractères)

### **Données d'entrée :**
- Catégorie de plainte
- Niveau de gravité
- Titre de la plainte
- Description détaillée
- Date et heure de l'incident
- Localisation
- Preuves (photos, documents)
- Demande de réparation
- Préférence d'anonymat

### **Données de sortie :**
- Numéro de référence de la plainte
- Statut de la plainte
- Message de confirmation
- Email de confirmation avec numéro de suivi




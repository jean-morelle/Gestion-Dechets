# Diagramme de Description Textuelle - Envoyer des Messages

## **Cas d'utilisation : Envoyer des Messages**

### **Acteur :**
- **Acteur principal :** Citoyen
- **Acteurs secondaires :** Administrateur, Collecteur (destinataires)

### **Pré-conditions :**
- Le citoyen est authentifié dans le système
- Le citoyen a accès à l'interface de messagerie
- Le système dispose d'un système de messagerie fonctionnel

### **Scénario nominal :**

1. **Accès à la messagerie**
   - Le citoyen clique sur "Messages" dans son tableau de bord
   - Le système affiche l'interface de messagerie
   - Le citoyen voit ses conversations existantes et peut créer un nouveau message

2. **Création d'un nouveau message**
   - Le citoyen clique sur "Nouveau message"
   - Le système affiche le formulaire de composition
   - Le citoyen sélectionne le destinataire (Administrateur, Collecteur, Support)

3. **Sélection du destinataire**
   - Le citoyen choisit dans une liste prédéfinie
   - Le système affiche les informations du destinataire
   - Le citoyen peut sélectionner un destinataire spécifique si applicable

4. **Composition du message**
   - Le citoyen saisit un objet/sujet du message
   - Le citoyen rédige le contenu du message
   - Le citoyen peut joindre des fichiers (photos, documents)

5. **Vérification et envoi**
   - Le citoyen relit son message
   - Le citoyen clique sur "Envoyer"
   - Le système valide le contenu et les pièces jointes

6. **Confirmation d'envoi**
   - Le système envoie le message
   - Le système affiche une confirmation d'envoi
   - Le destinataire reçoit une notification
   - Le message apparaît dans la conversation

### **Scénarios alternatifs :**

**2a. Réponse à un message existant**
- Le citoyen clique sur "Répondre" dans une conversation
- Le système pré-remplit le destinataire et l'objet
- Le citoyen compose sa réponse

**3a. Message lié à un signalement/plainte**
- Le citoyen peut envoyer un message lié à un signalement existant
- Le système pré-remplit le contexte
- Le message est automatiquement lié au dossier

**3b. Message urgent**
- Le citoyen marque le message comme "Urgent"
- Le système envoie une notification prioritaire
- Le message est traité en priorité par le destinataire

**4a. Pièces jointes**
- Le citoyen télécharge des fichiers
- Le système vérifie la taille et le format
- Si problème, affiche un message d'erreur avec les contraintes

**4b. Message long**
- Le citoyen rédige un message long
- Le système propose de sauvegarder en brouillon
- Le citoyen peut continuer plus tard

**5a. Contenu vide ou invalide**
- Le système vérifie que l'objet et le contenu ne sont pas vides
- Si problème, affiche un message d'erreur
- Le citoyen peut corriger et renvoyer

**5b. Destinataire indisponible**
- Le système vérifie la disponibilité du destinataire
- Si indisponible, propose d'envoyer quand même ou d'attendre
- Le message peut être mis en file d'attente

**6a. Erreur d'envoi**
- Le système affiche "Erreur lors de l'envoi. Veuillez réessayer."
- Le message est sauvegardé en brouillon
- Le citoyen peut réessayer plus tard

**6b. Message en attente de modération**
- Certains messages nécessitent une modération
- Le système informe que le message sera vérifié
- Le citoyen reçoit une notification quand le message est approuvé

### **Post-conditions :**

**En cas de succès :**
- Le message est envoyé au destinataire
- Une confirmation d'envoi est affichée
- Le message apparaît dans la conversation
- Le destinataire reçoit une notification

**En cas d'échec :**
- Le message n'est pas envoyé
- Un message d'erreur est affiché
- Le message est sauvegardé en brouillon
- Le citoyen peut réessayer

### **Contraintes :**
- Maximum 5 pièces jointes par message
- Taille maximale des fichiers : 10MB chacun
- Contenu minimum : 10 caractères
- Objet obligatoire (maximum 100 caractères)
- Messages archivés après 1 an

### **Données d'entrée :**
- Destinataire sélectionné
- Objet du message
- Contenu du message
- Pièces jointes (optionnelles)
- Priorité (normale, urgente)
- Lien avec dossier existant (optionnel)

### **Données de sortie :**
- Message envoyé
- Confirmation d'envoi
- Notification au destinataire
- Message dans la conversation
- Historique de messagerie

### **Types de destinataires :**
- **Administrateur** : Questions générales, réclamations
- **Support technique** : Problèmes techniques, aide
- **Collecteur** : Questions sur les collectes (si applicable)
- **Service client** : Informations, assistance

### **Fonctionnalités additionnelles :**
- Sauvegarde automatique en brouillon
- Recherche dans les messages
- Filtrage par destinataire et date
- Notifications de lecture
- Messages épinglés (importants)
- Export de conversation




# Diagramme de Description Textuelle - Gérer ses Notifications

## **Cas d'utilisation : Gérer ses Notifications**

### **Acteur :**
- **Acteur principal :** Citoyen
- **Acteurs secondaires :** Aucun (gestion personnelle)

### **Pré-conditions :**
- Le citoyen est authentifié dans le système
- Le citoyen a reçu des notifications
- Le citoyen a accès à l'interface de gestion des notifications

### **Scénario nominal :**

1. **Accès aux notifications**
   - Le citoyen clique sur l'icône de notification (badge avec nombre)
   - Le système affiche la liste des notifications non lues
   - Le citoyen peut accéder à "Toutes les notifications"

2. **Consultation des notifications**
   - Le système affiche les notifications par ordre chronologique
   - Chaque notification montre : type, titre, date, statut (lue/non lue)
   - Le citoyen peut voir un aperçu du contenu

3. **Lecture d'une notification**
   - Le citoyen clique sur une notification
   - Le système affiche le contenu complet
   - Le système marque automatiquement la notification comme lue
   - Le citoyen peut accéder à l'action liée (voir signalement, plainte, etc.)

4. **Gestion des notifications**
   - Le citoyen peut marquer une notification comme lue/non lue
   - Le citoyen peut supprimer une notification
   - Le citoyen peut marquer toutes les notifications comme lues

5. **Configuration des préférences**
   - Le citoyen accède aux paramètres de notifications
   - Il configure les types de notifications qu'il souhaite recevoir
   - Il configure les canaux de notification (email, SMS, push)

### **Scénarios alternatifs :**

**1a. Aucune notification**
- Le système affiche "Aucune notification"
- Un message d'encouragement est affiché
- Le citoyen peut configurer ses préférences

**2a. Notifications groupées**
- Le système groupe les notifications similaires
- Le citoyen peut développer un groupe pour voir les détails
- Les notifications anciennes sont automatiquement archivées

**3a. Notification avec action**
- Certaines notifications contiennent des boutons d'action
- Le citoyen peut directement répondre, confirmer, ou refuser
- L'action est traitée sans quitter la page des notifications

**3b. Notification expirée**
- Une notification a une date d'expiration
- Le système affiche "Expirée" et désactive les actions
- Le citoyen peut supprimer la notification expirée

**4a. Suppression en lot**
- Le citoyen sélectionne plusieurs notifications
- Il clique sur "Supprimer sélectionnées"
- Le système demande confirmation avant suppression

**4b. Archive automatique**
- Les notifications anciennes (> 30 jours) sont automatiquement archivées
- Le citoyen peut accéder aux archives
- Les notifications archivées peuvent être supprimées définitivement

**5a. Désactivation temporaire**
- Le citoyen peut désactiver temporairement certains types de notifications
- Il définit une période (vacances, absence, etc.)
- Les notifications sont suspendues puis reprennent automatiquement

**5b. Notifications urgentes**
- Certaines notifications urgentes ne peuvent pas être désactivées
- Elles s'affichent toujours en priorité
- Le citoyen peut seulement réduire leur fréquence

### **Post-conditions :**

**En cas de succès :**
- Les notifications sont mises à jour selon les actions du citoyen
- Les préférences sont sauvegardées
- Le citoyen a une vue claire de ses notifications importantes

**En cas d'échec :**
- Un message d'erreur est affiché
- Les modifications ne sont pas sauvegardées
- Le citoyen peut réessayer

### **Contraintes :**
- Maximum 1000 notifications stockées
- Archivage automatique après 30 jours
- Notifications urgentes toujours visibles
- Sauvegarde automatique des préférences

### **Données d'entrée :**
- Sélection de notifications à traiter
- Préférences de notification
- Paramètres de canal (email, SMS, push)
- Périodes de désactivation

### **Données de sortie :**
- Liste des notifications organisées
- Statut de lecture des notifications
- Préférences sauvegardées
- Confirmations d'actions

### **Types de notifications :**
- **Signalements** : Statut, traitement, résolution
- **Plaintes** : Réponse, suivi, fermeture
- **Collectes** : Planification, confirmation, rappel
- **Calendrier** : Modifications, annulations
- **Système** : Maintenance, nouvelles fonctionnalités
- **Campagnes** : Nouvelles campagnes de sensibilisation

### **Fonctionnalités additionnelles :**
- Recherche dans les notifications
- Filtrage par type et date
- Export des notifications importantes
- Intégration avec email et SMS




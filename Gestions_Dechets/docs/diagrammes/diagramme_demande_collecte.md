# Diagramme de Description Textuelle - Faire une Demande de Collecte

## **Cas d'utilisation : Faire une Demande de Collecte**

### **Acteur :**
- **Acteur principal :** Citoyen
- **Acteurs secondaires :** Administrateur (validation de la demande), Collecteur (exécution)

### **Pré-conditions :**
- Le citoyen est authentifié dans le système
- Le citoyen a des déchets à faire collecter
- Le citoyen a accès à l'interface de demande de collecte

### **Scénario nominal :**

1. **Accès au formulaire de demande**
   - Le citoyen clique sur "Demande de collecte" dans son tableau de bord
   - Le système affiche le formulaire de demande de collecte

2. **Sélection du type de collecte**
   - Le citoyen sélectionne le type de déchets (ménagers, encombrants, verts, recyclables, dangereux)
   - Le citoyen indique la quantité estimée
   - Le citoyen sélectionne l'unité de mesure (kg, litres, unités)

3. **Saisie des détails**
   - Le citoyen décrit les déchets à collecter
   - Le citoyen indique la localisation précise (adresse ou géolocalisation)
   - Le citoyen sélectionne la date souhaitée pour la collecte
   - Le citoyen précise la plage horaire préférée

4. **Ajout d'informations complémentaires**
   - Le citoyen télécharge des photos des déchets
   - Le citoyen ajoute des instructions spéciales (accès difficile, précautions, etc.)
   - Le citoyen indique ses coordonnées de contact

5. **Vérification des disponibilités**
   - Le système vérifie la disponibilité pour la date demandée
   - Le système propose des créneaux alternatifs si nécessaire
   - Le citoyen confirme ou modifie sa demande

6. **Soumission de la demande**
   - Le citoyen clique sur "Soumettre la demande"
   - Le système valide les informations saisies
   - Le système enregistre la demande avec un numéro de référence
   - Le système envoie une notification à l'administrateur

7. **Confirmation et planification**
   - Le système affiche un message de confirmation
   - Le système attribue un numéro de suivi
   - Le citoyen reçoit un email de confirmation
   - L'administrateur planifie la collecte

### **Scénarios alternatifs :**

**2a. Collecte urgente**
- Le citoyen sélectionne "Urgente"
- Le système propose des créneaux prioritaires
- Des frais supplémentaires peuvent s'appliquer

**3a. Géolocalisation automatique**
- Le citoyen autorise l'accès à sa position
- Le système remplit automatiquement l'adresse
- Le citoyen peut modifier si nécessaire

**3b. Date non disponible**
- Le système propose des dates alternatives
- Le citoyen peut choisir une autre date
- Le système affiche les prochaines disponibilités

**4a. Instructions d'accès spéciales**
- Le citoyen indique un accès difficile
- Le système demande des détails supplémentaires
- Des frais supplémentaires peuvent s'appliquer

**5a. Demande en dehors des heures de service**
- Le système informe que la demande sera traitée le prochain jour ouvré
- Le citoyen reçoit un accusé de réception automatique
- La demande est mise en file d'attente

**6a. Informations manquantes**
- Le système affiche les champs obligatoires non remplis
- Le citoyen complète les informations manquantes
- Il peut renvoyer le formulaire

**6b. Demande similaire récente**
- Le système détecte une demande similaire récente
- Il propose de consulter la demande existante
- Le citoyen peut confirmer ou modifier sa demande

**7a. Demande refusée**
- L'administrateur refuse la demande (motif technique, sécurité, etc.)
- Le citoyen reçoit une notification avec explication
- Le citoyen peut soumettre une nouvelle demande modifiée

### **Post-conditions :**

**En cas de succès :**
- La demande est enregistrée dans la base de données
- Un numéro de suivi est attribué
- L'administrateur est notifié pour planification
- Le citoyen peut suivre l'évolution dans "Mes Demandes"

**En cas d'échec :**
- La demande n'est pas enregistrée
- Un message d'erreur est affiché
- Le citoyen peut réessayer

### **Contraintes :**
- Maximum 5 photos par demande
- Taille maximale des photos : 5MB chacune
- Date de collecte minimum : 24h à l'avance
- Quantité maximum selon le type de déchets

### **Données d'entrée :**
- Type de déchets
- Quantité estimée et unité
- Description des déchets
- Localisation précise
- Date souhaitée
- Plage horaire préférée
- Photos (optionnelles)
- Instructions spéciales
- Coordonnées de contact

### **Données de sortie :**
- Numéro de référence de la demande
- Statut de la demande
- Date et heure confirmées
- Message de confirmation
- Email de confirmation avec détails




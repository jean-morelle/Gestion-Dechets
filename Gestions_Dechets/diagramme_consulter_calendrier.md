# Diagramme de Description Textuelle - Consulter le Calendrier de Collecte

## **Cas d'utilisation : Consulter le Calendrier de Collecte**

### **Acteur :**
- **Acteur principal :** Citoyen
- **Acteurs secondaires :** Aucun (lecture seule)

### **Pré-conditions :**
- Le citoyen est authentifié dans le système
- Le système contient des données de calendrier de collecte
- Le citoyen a accès à l'interface du calendrier

### **Scénario nominal :**

1. **Accès au calendrier**
   - Le citoyen clique sur "Calendrier de collecte" dans son tableau de bord
   - Le système affiche le calendrier de collecte

2. **Affichage du calendrier**
   - Le système affiche le mois en cours par défaut
   - Le système montre les jours de collecte avec des codes couleur
   - Le système affiche les types de collecte (ménagers, recyclables, verts, etc.)

3. **Navigation dans le calendrier**
   - Le citoyen peut naviguer entre les mois (précédent/suivant)
   - Le citoyen peut sélectionner une date spécifique
   - Le système met à jour l'affichage selon la sélection

4. **Consultation des détails**
   - Le citoyen clique sur un jour de collecte
   - Le système affiche les détails (type de déchets, horaires, instructions)
   - Le système montre les informations complémentaires

5. **Filtrage des informations**
   - Le citoyen peut filtrer par type de collecte
   - Le citoyen peut filtrer par zone/quartier
   - Le système met à jour l'affichage selon les filtres

### **Scénarios alternatifs :**

**1a. Accès via notification**
- Le citoyen reçoit une notification de changement de calendrier
- Il clique sur la notification pour accéder directement au calendrier
- Le système affiche le calendrier avec les modifications mises en évidence

**2a. Calendrier vide**
- Le système n'a pas de données pour la période demandée
- Un message "Aucune collecte planifiée" est affiché
- Le citoyen peut changer de mois ou contacter l'administration

**3a. Recherche de date spécifique**
- Le citoyen utilise la fonction de recherche de date
- Il saisit une date spécifique
- Le système affiche directement le mois et la date demandée

**4a. Informations détaillées**
- Le citoyen clique sur "Plus de détails"
- Le système affiche une popup ou une nouvelle page
- Informations complètes : horaires, types, zones, contacts, etc.

**4b. Instructions spéciales**
- Certains jours ont des instructions spéciales (fêtes, travaux, etc.)
- Le système affiche ces informations avec une icône d'alerte
- Le citoyen peut cliquer pour voir les détails

**5a. Filtrage par type de déchets**
- Le citoyen sélectionne "Déchets ménagers uniquement"
- Le calendrier ne montre que les jours de collecte ménagers
- Les autres types sont grisés ou masqués

**5b. Filtrage par zone**
- Le citoyen sélectionne sa zone/quartier
- Le système affiche uniquement les collectes de sa zone
- Les autres zones sont masquées

### **Post-conditions :**

**En cas de succès :**
- Le citoyen consulte les informations de collecte
- Le citoyen peut planifier ses dépôts de déchets
- Le citoyen est informé des prochaines collectes

**En cas d'échec :**
- Un message d'erreur est affiché
- Le citoyen peut réessayer ou contacter l'administration

### **Contraintes :**
- Affichage maximum de 12 mois à l'avance
- Mise à jour du calendrier en temps réel
- Informations disponibles 24h/24

### **Données d'entrée :**
- Mois/année sélectionné
- Filtres appliqués (type, zone)
- Date spécifique recherchée

### **Données de sortie :**
- Calendrier mensuel avec jours de collecte
- Types de collecte par jour
- Horaires de collecte
- Instructions spéciales
- Informations de contact
- Légende des codes couleur

### **Fonctionnalités additionnelles :**
- Export du calendrier (PDF, image)
- Notification des prochaines collectes
- Intégration avec agenda personnel
- Rappels automatiques




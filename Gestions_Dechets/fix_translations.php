<?php
/**
 * Script pour remplacer automatiquement les clés de traduction par du français
 * dans tous les fichiers Blade de l'application
 */

// Dictionnaire de traductions françaises
$translations = [
    // Navigation et menus
    'nav.dashboard' => 'Tableau de bord',
    'nav.my_reports' => 'Mes Signalements',
    'nav.collection_requests' => 'Demandes de Collecte',
    'nav.my_complaints' => 'Mes Plaintes',
    'nav.calendar' => 'Calendrier',
    'nav.awareness_campaigns' => 'Campagnes de Sensibilisation',
    'nav.notifications' => 'Notifications',
    'nav.messages' => 'Messages',
    'nav.my_routes' => 'Mes Itinéraires',
    'nav.my_collections' => 'Mes Collectes',
    'nav.incidents' => 'Incidents',
    'nav.reports' => 'Signalements',
    'nav.users' => 'Utilisateurs',
    'nav.complaints' => 'Plaintes',
    'nav.routes' => 'Itinéraires',
    'nav.supervision' => 'Supervision',
    'nav.campaigns' => 'Campagnes',
    'nav.my_profile' => 'Mon Profil',
    'nav.settings' => 'Paramètres',
    'nav.logout' => 'Déconnexion',
    
    // Dashboard
    'dashboard.welcome' => 'Bonjour',
    'dashboard.users' => 'Utilisateurs',
    'dashboard.reports' => 'Signalements',
    'dashboard.complaints' => 'Plaintes',
    'dashboard.routes' => 'Itinéraires',
    'dashboard.collections' => 'Collectes',
    'dashboard.in_progress' => 'En cours',
    'dashboard.notifications' => 'Notifications',
    'dashboard.reports_evolution' => 'Évolution des signalements',
    'dashboard.waste_types' => 'Types de déchets',
    'dashboard.recent_reports' => 'Signalements récents',
    'dashboard.collections_evolution' => 'Évolution des collectes',
    'dashboard.collections_status' => 'Statut des collectes',
    'dashboard.recent_routes' => 'Itinéraires récents',
    'dashboard.recent_collections' => 'Collectes récentes',
    'dashboard.requests' => 'Demandes',
    'dashboard.upcoming_collections' => 'Prochaines collectes',
    'dashboard.my_reports_evolution' => 'Évolution de mes signalements',
    'dashboard.my_reports_status' => 'Statut de mes signalements',
    'dashboard.recent_activities' => 'Activités récentes',
    
    // Signalements
    'reports.title' => 'Signalements',
    'reports.my_reports' => 'Mes Signalements',
    'reports.intro_my_reports' => 'Consultez l\'historique de vos signalements et leur statut.',
    'reports.new_report' => 'Nouveau Signalement',
    'reports.create_report' => 'Créer un Signalement',
    'reports.waste_type' => 'Type de déchet',
    'reports.description' => 'Description',
    'reports.status' => 'Statut',
    'reports.created_at' => 'Créé le',
    'reports.priority' => 'Priorité',
    'reports.photo' => 'Photo',
    'reports.pending' => 'En attente',
    'reports.in_progress' => 'En cours',
    'reports.processed' => 'Traités',
    'reports.low' => 'Faible',
    'reports.medium' => 'Moyenne',
    'reports.high' => 'Élevée',
    'reports.urgent' => 'Urgente',
    'reports.household_waste' => 'Déchets ménagers',
    'reports.green_waste' => 'Déchets verts',
    'reports.bulky_waste' => 'Encombrants',
    'reports.hazardous_waste' => 'Déchets dangereux',
    'reports.recyclable_waste' => 'Recyclables',
    'reports.electronic' => 'Déchets dangereux/électroniques',
    'reports.other' => 'Autre',
    'reports.select_waste_type' => 'Sélectionner le type de déchet',
    'reports.no_reports' => 'Aucun signalement trouvé',
    'reports.gps_coordinates' => 'Position GPS',
    'reports.latitude' => 'Latitude',
    'reports.longitude' => 'Longitude',
    
    // Plaintes
    'complaints.title' => 'Plaintes',
    'complaints.my_complaints' => 'Mes Plaintes',
    'complaints.new_complaint' => 'Nouvelle Plainte',
    'complaints.complaint_type' => 'Type de plainte',
    'complaints.object' => 'Objet',
    'complaints.status' => 'Statut',
    'complaints.processed' => 'Traitées',
    'complaints.no_complaints' => 'Aucune plainte trouvée',
    
    // Collectes
    'collections.title' => 'Collectes',
    'collections.my_collections' => 'Mes Collectes',
    'collections.my_requests' => 'Mes Demandes de Collecte',
    'collections.new_collection' => 'Nouvelle Collecte',
    'collections.new_request' => 'Nouvelle Demande',
    'collections.create_request' => 'Effectuer la première demande',
    'collections.collection_type' => 'Type de collecte',
    'collections.collection_details' => 'Détails de la Collecte',
    'collections.request_details' => 'Détails de la Demande',
    'collections.request_subject' => 'Objet de la demande',
    'collections.description' => 'Description',
    'collections.date' => 'Date',
    'collections.status' => 'Statut',
    'collections.scheduled' => 'Prévue',
    'collections.in_progress' => 'En cours',
    'collections.completed' => 'Terminée',
    'collections.household' => 'Ménagère',
    'collections.recyclable' => 'Recyclable',
    'collections.bulky' => 'Encombrants',
    'collections.green_waste' => 'Déchets verts',
    'collections.green' => 'Végétal',
    'collections.special' => 'Spécial',
    'collections.requested_date' => 'Date souhaitée',
    'collections.requested_time' => 'Heure souhaitée',
    'collections.no_requests' => 'Aucune demande de collecte',
    'collections.collected_quantity' => 'Quantité collectée',
    'collections.start_time' => 'Heure de début',
    'collections.end_time' => 'Heure de fin',
    'collections.estimated_duration' => 'Durée de collecte',
    'collections.distance' => 'Distance parcourue',
    'collections.gps_validation' => 'Validation GPS',
    'collections.validated' => 'Validée',
    'collections.not_validated' => 'Non validée',
    'collections.notes' => 'Notes',
    'collections.photos' => 'Photos',
    'collections.photo_before' => 'Photo avant',
    'collections.photo_after' => 'Photo après',
    'collections.collection_point' => 'Point de collecte',
    'collections.name' => 'Nom',
    'collections.point_type' => 'Type de point',
    'collections.capacity' => 'Capacité',
    'collections.scheduled_date' => 'Date prévue',
    'collections.start_collection' => 'Démarrer la collecte',
    'collections.validate_collection' => 'Valider la collecte',
    'collections.update_status' => 'Mettre à jour le statut',
    'collections.completed' => 'Collecte terminée',
    'collections.report_incident' => 'Signaler un incident',
    'collections.collector_info' => 'Informations du Collecteur',
    'collections.new_status' => 'Nouveau statut',
    'collections.paused' => 'En pause',
    'collections.add_notes' => 'Ajoutez des notes sur la collecte...',
    'collections.validation_photo' => 'Photo de validation',
    'collections.gps_will_be_saved' => 'Votre position GPS sera enregistrée automatiquement.',
    'collections.geolocation_not_supported' => 'La géolocalisation n\'est pas supportée.',
    'collections.fetching_position' => 'Récupération de votre position...',
    'collections.validate' => 'Valider',
    'collections.not_computed' => 'Non calculée',
    
    // Itinéraires
    'routes.title' => 'Itinéraires',
    'routes.my_routes' => 'Mes Itinéraires',
    'routes.new_route' => 'Créer un itinéraire',
    'routes.name' => 'Nom',
    'routes.description' => 'Description',
    'routes.type' => 'Type',
    'routes.collector' => 'Collecteur',
    'routes.status' => 'Statut',
    'routes.scheduled_date' => 'Date prévue',
    'routes.scheduled' => 'Planifié',
    'routes.completed' => 'Terminé',
    'routes.dates' => 'Dates',
    'routes.hours' => 'Heures',
    'routes.start' => 'Démarrer',
    'routes.no_routes' => 'Aucun itinéraire pour l\'instant.',
    'routes.assigned_by_admin' => 'Les itinéraires vous seront assignés par l\'administrateur.',
    'routes.route_details' => 'Informations de l\'Itinéraire',
    'routes.view_route' => 'Voir l\'itinéraire',
    
    // Messages
    'messages.title' => 'Messages',
    'messages.communication' => 'Communication',
    'messages.new_message' => 'Nouveau Message',
    'messages.received_messages' => 'Messages reçus',
    'messages.sent_messages' => 'Messages envoyés',
    'messages.no_received' => 'Aucun message reçu',
    'messages.no_sent' => 'Aucun message envoyé',
    
    // Paramètres
    'settings.title' => 'Paramètres',
    'settings.notifications' => 'Notifications',
    
    // Application générale
    'app.actions' => 'Actions',
    'app.status' => 'Statut',
    'app.date' => 'Date',
    'app.back' => 'Retour',
    'app.back_to_list' => 'Retour à la liste',
    'app.details' => 'Détails',
    'app.open' => 'Ouvrir',
    'app.save' => 'Enregistrer',
    'app.cancel' => 'Annuler',
    'app.update' => 'Mettre à jour',
    'app.send' => 'Envoyer',
    'app.next' => 'Suivant',
    'app.all' => 'Tous',
    'app.all_statuses' => 'Tous les statuts',
    'app.all_types' => 'Tous les types',
    'app.select' => 'Sélectionner',
    'app.select_type' => 'Sélectionner un type',
    'app.select_status' => 'Sélectionnez un statut',
    'app.select_urgency' => 'Sélectionner l\'urgence',
    'app.address' => 'Adresse',
    'app.district' => 'Quartier',
    'app.phone' => 'Téléphone',
    'app.information' => 'Informations',
    'app.created_at' => 'Créé le',
    'app.updated_at' => 'Dernière mise à jour',
    'app.by' => 'Par',
    'app.and_actions' => 'et Actions',
    'app.current_status' => 'Statut actuel',
    'app.not_specified' => 'Non spécifié',
    'app.not_assigned' => 'Non assigné',
    'app.not_defined' => 'Non défini',
    'app.unknown_user' => 'Utilisateur inconnu',
    'app.optional' => 'optionnel',
    
    // Supervision
    'app.supervision' => 'Supervision',
    'app.operations_control' => 'Contrôle des opérations',
    'app.pending_issues' => 'Problèmes en attente',
    'app.current_activity' => 'Activité actuelle',
    'app.total' => 'Total',
    
    // Urgences
    'common.urgency' => 'Urgence',
    'common.normal' => 'Normale',
    'common.critical' => 'Critique',
    'common.in_progress' => 'En cours',
    'common.cancelled' => 'Annulé',
    
    // Utilisateurs
    'users.name' => 'Nom',
    'users.citizen' => 'Citoyen',
];

// Fonction pour remplacer les clés de traduction dans un fichier
function replaceTranslations($filePath, $translations) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $changes = 0;
    
    foreach ($translations as $key => $french) {
        // Pattern pour {{ __('key') }}
        $pattern1 = '/\{\{\s*__\(\s*[\'"]' . preg_quote($key, '/') . '[\'"]\s*\)\s*\}\}/';
        $replacement1 = $french;
        
        // Pattern pour {{ __('key') ?? 'fallback' }}
        $pattern2 = '/\{\{\s*__\(\s*[\'"]' . preg_quote($key, '/') . '[\'"]\s*\)\s*\?\?\s*[\'"].*?[\'"]\s*\}\}/';
        $replacement2 = $french;
        
        $newContent = preg_replace($pattern1, $replacement1, $content);
        $newContent = preg_replace($pattern2, $replacement2, $newContent);
        
        if ($newContent !== $content) {
            $content = $newContent;
            $changes++;
        }
    }
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "✓ Modifié: $filePath ($changes changements)\n";
        return true;
    }
    
    return false;
}

// Fonction pour scanner récursivement les fichiers Blade
function scanBladeFiles($directory, $translations) {
    $files = glob($directory . '/*.blade.php');
    $totalFiles = 0;
    $modifiedFiles = 0;
    
    foreach ($files as $file) {
        $totalFiles++;
        if (replaceTranslations($file, $translations)) {
            $modifiedFiles++;
        }
    }
    
    // Scanner les sous-dossiers
    $subdirs = glob($directory . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        $result = scanBladeFiles($subdir, $translations);
        $totalFiles += $result['total'];
        $modifiedFiles += $result['modified'];
    }
    
    return ['total' => $totalFiles, 'modified' => $modifiedFiles];
}

echo "=== Script de remplacement des traductions ===\n";
echo "Recherche des fichiers Blade...\n\n";

$viewsDir = __DIR__ . '/resources/views';
$result = scanBladeFiles($viewsDir, $translations);

echo "\n=== Résumé ===\n";
echo "Fichiers traités: {$result['total']}\n";
echo "Fichiers modifiés: {$result['modified']}\n";
echo "Terminé!\n";
?>







<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitoyenController;
use App\Http\Controllers\CollecteurController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TwoFactorController;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
|
| Ici sont définies les routes web pour l'application. Ces routes sont
| chargées par le RouteServiceProvider dans un groupe qui contient le
| middleware "web".
|
*/

// Route d'accueil
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'citoyen':
                return redirect()->route('citoyen.dashboard');
            case 'collecteur':
                return redirect()->route('collecteur.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('login')->with('error', 'Rôle non reconnu');
        }
    }
    return redirect()->route('login');
});

// Routes d'authentification
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Routes Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Routes collecteur
Route::middleware(['auth', 'role:collecteur'])->group(function () {
    Route::get('/collecteur/dashboard', [App\Http\Controllers\CollecteurController::class, 'dashboard'])->name('collecteur.dashboard');
    
    // Route de test locale: créer un itinéraire avec 3 points et y associer des collectes en attente
    if (app()->environment('local')) {
        Route::get('/collecteur/dev/seed', function () {
            $user = auth()->user();
            if (!$user || $user->role !== 'collecteur') {
                return redirect()->route('login')->with('error', 'Connectez-vous en tant que collecteur');
            }

            // Créer trois points de collecte simples
            $points = [];
            $base = [
                ['nom' => 'Point A', 'adresse' => 'Adresse A', 'lat' => 14.6937, 'lng' => -17.4441],
                ['nom' => 'Point B', 'adresse' => 'Adresse B', 'lat' => 14.7000, 'lng' => -17.4500],
                ['nom' => 'Point C', 'adresse' => 'Adresse C', 'lat' => 14.7050, 'lng' => -17.4600],
            ];
            foreach ($base as $b) {
                $points[] = \App\Models\PointDeCollecte::create([
                    'nom' => $b['nom'],
                    'type' => \App\Models\PointDeCollecte::TYPE_PUBLIC,
                    'adresse' => $b['adresse'],
                    'quartier' => 'Centre',
                    'latitude' => $b['lat'],
                    'longitude' => $b['lng'],
                    'statut' => \App\Models\PointDeCollecte::STATUT_ACTIF,
                ]);
            }

            // Créer un itinéraire et attacher les points avec ordre
            $itineraire = \App\Models\Itineraire::create([
                'nom' => 'Tournée de test',
                'type' => 'ponctuel',
                'description' => 'Généré pour test',
                'collecteur_id' => $user->id,
                'date_debut' => now()->startOfDay(),
                'date_fin' => now()->endOfDay(),
                'heure_debut' => now(),
                'heure_fin' => now()->addHours(2),
                'statut' => 'planifie',
                'distance_estimee' => 2.5,
                'duree_estimee' => 30,
                'admin_id' => $user->id,
            ]);

            foreach ($points as $i => $p) {
                $itineraire->pointsDeCollecte()->attach($p->id, ['ordre' => $i + 1]);
            }

            // Démarrer l'itinéraire pour auto-créer les collectes en_attente
            app(\App\Http\Controllers\CollecteurController::class)->demarrerItineraire($itineraire);

            return redirect()->route('collecteur.itineraires.show', [$itineraire->id, 'tab' => 'points'])
                ->with('success', 'Données de test créées.');
        })->name('collecteur.dev.seed');
    }
    
    // Routes collectes
    Route::get('/collecteur/collectes', [App\Http\Controllers\CollecteurController::class, 'indexCollectes'])->name('collecteur.collectes.index');
    Route::get('/collecteur/collectes/{collecte}', [App\Http\Controllers\CollecteurController::class, 'showCollecte'])->name('collecteur.collectes.show');
    Route::post('/collecteur/collectes/{collecte}/start', [App\Http\Controllers\CollecteurController::class, 'startCollection'])->name('collecteur.collectes.start');
    Route::put('/collecteur/collectes/{collecte}/validate', [App\Http\Controllers\CollecteurController::class, 'validerCollecte'])->name('collecteur.collectes.validate');
    Route::put('/collecteur/collectes/{collecte}/update', [App\Http\Controllers\CollecteurController::class, 'mettreAJourCollecte'])->name('collecteur.collectes.mettre-a-jour');
    Route::put('/collecteur/collectes/{collecte}/status', [App\Http\Controllers\CollecteurController::class, 'updateCollecteStatus'])->name('collecteur.collectes.update-status');
    
    // Routes itinéraires
    Route::get('/collecteur/itineraires', [App\Http\Controllers\CollecteurController::class, 'consulterItineraires'])->name('collecteur.itineraires.index');
    Route::get('/collecteur/itineraires/{itineraire}', [App\Http\Controllers\CollecteurController::class, 'afficherItineraire'])->name('collecteur.itineraires.show');
    Route::post('/collecteur/itineraires/{itineraire}/demarrer', [App\Http\Controllers\CollecteurController::class, 'demarrerItineraire'])->name('collecteur.itineraires.demarrer');
    Route::post('/collecteur/itineraires/{itineraire}/terminer', [App\Http\Controllers\CollecteurController::class, 'terminerItineraire'])->name('collecteur.itineraires.terminer');
    
    // Routes incidents
    Route::get('/collecteur/incidents', [App\Http\Controllers\CollecteurController::class, 'indexIncidents'])->name('collecteur.incidents.index');
    Route::get('/collecteur/incidents/create', [App\Http\Controllers\CollecteurController::class, 'createIncident'])->name('collecteur.incidents.create');
    Route::post('/collecteur/incidents', [App\Http\Controllers\CollecteurController::class, 'storeIncident'])->name('collecteur.incidents.store');
    Route::get('/collecteur/incidents/{incident}', [App\Http\Controllers\CollecteurController::class, 'showIncident'])->name('collecteur.incidents.show');
    Route::get('/collecteur/incidents/{incident}/edit', [App\Http\Controllers\CollecteurController::class, 'editIncident'])->name('collecteur.incidents.edit');
    Route::put('/collecteur/incidents/{incident}', [App\Http\Controllers\CollecteurController::class, 'updateIncident'])->name('collecteur.incidents.update');
    Route::delete('/collecteur/incidents/{incident}', [App\Http\Controllers\CollecteurController::class, 'destroyIncident'])->name('collecteur.incidents.destroy');
    
    // Routes notifications
    Route::get('/collecteur/notifications', [App\Http\Controllers\CollecteurController::class, 'indexNotifications'])->name('collecteur.notifications.index');
    Route::post('/collecteur/notifications/{notification}/marquer-lue', [App\Http\Controllers\CollecteurController::class, 'marquerNotificationLue'])->name('collecteur.notifications.marquer-lue');
    Route::post('/collecteur/notifications/marquer-toutes-lues', [App\Http\Controllers\CollecteurController::class, 'marquerToutesNotificationsLues'])->name('collecteur.notifications.marquer-toutes-lues');
    
    // Routes campagnes de sensibilisation
    Route::get('/collecteur/campagnes', [App\Http\Controllers\CollecteurController::class, 'indexCampagnes'])->name('collecteur.campagnes.index');
    Route::get('/collecteur/campagnes/{campagne}', [App\Http\Controllers\CollecteurController::class, 'showCampagne'])->name('collecteur.campagnes.show');
    Route::post('/collecteur/campagnes/{campagne}/partager', [App\Http\Controllers\CollecteurController::class, 'partagerCampagne'])->name('collecteur.campagnes.partager');
    Route::get('/collecteur/campagnes/{campagne}/partage-mobile', [App\Http\Controllers\CollecteurController::class, 'partageMobile'])->name('collecteur.campagnes.partage-mobile');
});

// Routes admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Routes utilisateurs
    Route::get('/admin/utilisateurs', [App\Http\Controllers\AdminController::class, 'gererComptes'])->name('admin.utilisateurs.index');
    Route::get('/admin/utilisateurs/{user}', [App\Http\Controllers\AdminController::class, 'afficherUtilisateur'])->name('admin.utilisateurs.show');
    Route::post('/admin/utilisateurs/{user}/statut', [App\Http\Controllers\AdminController::class, 'modifierStatutUtilisateur'])->name('admin.utilisateurs.statut');
    Route::delete('/admin/utilisateurs/{user}', [App\Http\Controllers\AdminController::class, 'destroyUtilisateur'])->name('admin.utilisateurs.destroy');
    
    // Routes signalements
    Route::get('/admin/signalements', [App\Http\Controllers\AdminController::class, 'gererSignalements'])->name('admin.signalements.index');
    Route::get('/admin/signalements/{signalement}', [App\Http\Controllers\AdminController::class, 'afficherSignalement'])->name('admin.signalements.show');
    Route::put('/admin/signalements/{signalement}', [App\Http\Controllers\AdminController::class, 'updateSignalement'])->name('admin.signalements.update');
    Route::delete('/admin/signalements/{signalement}', [App\Http\Controllers\AdminController::class, 'destroySignalement'])->name('admin.signalements.destroy');
    Route::post('/admin/signalements/{signalement}/traiter', [App\Http\Controllers\AdminController::class, 'traiterSignalement'])->name('admin.signalements.traiter');
    
    // Routes plaintes
    Route::get('/admin/plaintes', [App\Http\Controllers\AdminController::class, 'gererPlaintes'])->name('admin.plaintes.index');
    Route::get('/admin/plaintes/{plainte}', [App\Http\Controllers\AdminController::class, 'afficherPlainte'])->name('admin.plaintes.show');
    Route::post('/admin/plaintes/{plainte}/traiter', [App\Http\Controllers\AdminController::class, 'traiterPlainte'])->name('admin.plaintes.traiter');
    Route::delete('/admin/plaintes/{plainte}', [App\Http\Controllers\AdminController::class, 'destroyPlainte'])->name('admin.plaintes.destroy');
    Route::post('/admin/plaintes/{plainte}/fermer', [App\Http\Controllers\AdminController::class, 'fermerPlainte'])->name('admin.plaintes.fermer');
    
    // Routes itinéraires
    Route::get('/admin/itineraires', [App\Http\Controllers\AdminController::class, 'gererItineraires'])->name('admin.itineraires.index');
    Route::get('/admin/itineraires/create', [App\Http\Controllers\AdminController::class, 'createItineraire'])->name('admin.itineraires.create');
    Route::post('/admin/itineraires', [App\Http\Controllers\AdminController::class, 'storeItineraire'])->name('admin.itineraires.store');
    Route::get('/admin/itineraires/{itineraire}/edit', [App\Http\Controllers\AdminController::class, 'editItineraire'])->name('admin.itineraires.edit');
    Route::put('/admin/itineraires/{itineraire}', [App\Http\Controllers\AdminController::class, 'updateItineraire'])->name('admin.itineraires.update');
    Route::delete('/admin/itineraires/{itineraire}', [App\Http\Controllers\AdminController::class, 'destroyItineraire'])->name('admin.itineraires.destroy');
    
    // Routes calendrier
    Route::get('/admin/calendrier', [App\Http\Controllers\AdminController::class, 'gererCalendrier'])->name('admin.calendrier.index');
    Route::get('/admin/calendrier/create', [App\Http\Controllers\AdminController::class, 'createCalendrier'])->name('admin.calendrier.create');
    Route::post('/admin/calendrier', [App\Http\Controllers\AdminController::class, 'storeCalendrier'])->name('admin.calendrier.store');
    
    // Routes supervision
    Route::get('/admin/supervision', [App\Http\Controllers\AdminController::class, 'superviserOperations'])->name('admin.supervision.index');
    
    // Routes notifications admin
    Route::get('/admin/notifications', [App\Http\Controllers\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/create', [App\Http\Controllers\AdminNotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/admin/notifications', [App\Http\Controllers\AdminNotificationController::class, 'store'])->name('admin.notifications.store');
    Route::get('/admin/notifications/{notification}', [App\Http\Controllers\AdminNotificationController::class, 'show'])->name('admin.notifications.show');
    Route::delete('/admin/notifications/{notification}', [App\Http\Controllers\AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::get('/admin/notifications/urgence/create', [App\Http\Controllers\AdminNotificationController::class, 'createUrgence'])->name('admin.notifications.urgence');
    Route::post('/admin/notifications/urgence', [App\Http\Controllers\AdminNotificationController::class, 'storeUrgence'])->name('admin.notifications.store-urgence');
    
    // Routes campagnes admin
    Route::get('/admin/campagnes', [App\Http\Controllers\AdminCampagneController::class, 'index'])->name('admin.campagnes.index');
    Route::get('/admin/campagnes/create', [App\Http\Controllers\AdminCampagneController::class, 'create'])->name('admin.campagnes.create');
    Route::post('/admin/campagnes', [App\Http\Controllers\AdminCampagneController::class, 'store'])->name('admin.campagnes.store');
    Route::get('/admin/campagnes/{campagne}', [App\Http\Controllers\AdminCampagneController::class, 'show'])->name('admin.campagnes.show');
    Route::get('/admin/campagnes/{campagne}/edit', [App\Http\Controllers\AdminCampagneController::class, 'edit'])->name('admin.campagnes.edit');
    Route::put('/admin/campagnes/{campagne}', [App\Http\Controllers\AdminCampagneController::class, 'update'])->name('admin.campagnes.update');
    Route::delete('/admin/campagnes/{campagne}', [App\Http\Controllers\AdminCampagneController::class, 'destroy'])->name('admin.campagnes.destroy');
    Route::post('/admin/campagnes/{campagne}/publier', [App\Http\Controllers\AdminCampagneController::class, 'publier'])->name('admin.campagnes.publier');
    Route::post('/admin/campagnes/{campagne}/archiver', [App\Http\Controllers\AdminCampagneController::class, 'archiver'])->name('admin.campagnes.archiver');
    Route::get('/admin/campagnes/{campagne}/statistiques', [App\Http\Controllers\AdminCampagneController::class, 'statistiques'])->name('admin.campagnes.statistiques');
});

// Routes pour la messagerie (accessibles à tous les utilisateurs connectés)
Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [App\Http\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/{message}/reply', [App\Http\Controllers\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/reply', [App\Http\Controllers\MessageController::class, 'replyStore'])->name('messages.reply.store');
    Route::post('/messages/{message}/mark-as-read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
    Route::delete('/messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
});

// Routes d'inscription
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Route de redirection après connexion
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login');
    }
    
    switch ($user->role) {
        case 'citoyen':
            return redirect()->route('citoyen.dashboard');
        case 'collecteur':
            return redirect()->route('collecteur.dashboard');
        case 'admin':
            return redirect()->route('admin.dashboard');
        default:
            return redirect()->route('login');
    }
})->middleware('auth')->name('dashboard');

// Routes pour le profil et les paramètres (authentifiées)
Route::middleware('auth')->group(function () {
    // Paramètres
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance.update');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    
    // Authentification à deux facteurs
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::get('/two-factor/enable', [TwoFactorController::class, 'show'])->name('two-factor.enable.get');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::get('/two-factor/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');
    Route::post('/two-factor/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes.regenerate');
});

/*
|--------------------------------------------------------------------------
| Routes Citoyen
|--------------------------------------------------------------------------
*/
Route::prefix('citoyen')->name('citoyen.')->middleware(['auth', 'role:citoyen'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [CitoyenController::class, 'dashboard'])->name('dashboard');
    
    // Signalements
    Route::get('/signalements', [CitoyenController::class, 'listerSignalements'])->name('signalements.index');
    Route::get('/signalements/creer', [CitoyenController::class, 'creerSignalement'])->name('signalements.create');
    Route::post('/signalements', [CitoyenController::class, 'enregistrerSignalement'])->name('signalements.store');
    Route::get('/signalements/{signalement}', [CitoyenController::class, 'afficherSignalement'])->name('signalements.show');
    
    // Plaintes
    Route::get('/plaintes', [CitoyenController::class, 'listerPlaintes'])->name('plaintes.index');
    Route::get('/plaintes/creer', [CitoyenController::class, 'creerPlainte'])->name('plaintes.create');
    Route::post('/plaintes', [CitoyenController::class, 'enregistrerPlainte'])->name('plaintes.store');
    Route::get('/plaintes/{plainte}', [CitoyenController::class, 'afficherPlainte'])->name('plaintes.show');
    
    // Demandes de collecte
    Route::get('/demandes-collecte', [CitoyenController::class, 'listerDemandesCollecte'])->name('demandes-collecte.index');
    Route::get('/demandes-collecte/creer', [CitoyenController::class, 'creerDemandeCollecte'])->name('demandes-collecte.create');
    Route::post('/demandes-collecte', [CitoyenController::class, 'enregistrerDemandeCollecte'])->name('demandes-collecte.store');
    Route::get('/demandes-collecte/{demandeCollecte}', [CitoyenController::class, 'afficherDemandeCollecte'])->name('demandes-collecte.show');
    
    // Calendrier
    Route::get('/calendrier', [CitoyenController::class, 'consulterCalendrier'])->name('calendrier.index');
    Route::get('/calendrier/prochaines-collectes', [CitoyenController::class, 'prochainesCollectes'])->name('calendrier.prochaines');
    
    // Rappels automatiques
    Route::get('/rappels', [App\Http\Controllers\RappelController::class, 'index'])->name('rappels.index');
    Route::get('/rappels/create', [App\Http\Controllers\RappelController::class, 'create'])->name('rappels.create');
    Route::post('/rappels', [App\Http\Controllers\RappelController::class, 'store'])->name('rappels.store');
    Route::get('/rappels/{rappel}', [App\Http\Controllers\RappelController::class, 'show'])->name('rappels.show');
    Route::get('/rappels/{rappel}/edit', [App\Http\Controllers\RappelController::class, 'edit'])->name('rappels.edit');
    Route::put('/rappels/{rappel}', [App\Http\Controllers\RappelController::class, 'update'])->name('rappels.update');
    Route::delete('/rappels/{rappel}', [App\Http\Controllers\RappelController::class, 'destroy'])->name('rappels.destroy');
    Route::post('/rappels/{rappel}/toggle', [App\Http\Controllers\RappelController::class, 'toggle'])->name('rappels.toggle');
    
    // Campagnes de sensibilisation
    Route::get('/campagnes', [CitoyenController::class, 'indexCampagnes'])->name('campagnes.index');
    Route::get('/campagnes/{campagne}', [CitoyenController::class, 'showCampagne'])->name('campagnes.show');
    Route::post('/campagnes/{campagne}/partager', [CitoyenController::class, 'partagerCampagne'])->name('campagnes.partager');
    
    // Notifications
    Route::get('/notifications', [CitoyenController::class, 'indexNotifications'])->name('notifications.index');
    Route::post('/notifications/{notificationId}/marquer-lue', [CitoyenController::class, 'marquerNotificationLue'])->name('notifications.marquer-lue');
    Route::post('/notifications/marquer-toutes-lues', [CitoyenController::class, 'marquerToutesNotificationsLues'])->name('notifications.marquer-toutes-lues');
    
    Route::get('/campagnes/type/{type}', [App\Http\Controllers\CampagneController::class, 'parType'])->name('campagnes.type');
    Route::get('/notifications/nombre-non-lues', [CitoyenController::class, 'nombreNotificationsNonLues'])->name('notifications.nombre-non-lues');
    
    // Profil
    Route::get('/profil', [CitoyenController::class, 'profil'])->name('profil');
    
    // Paramètres
});

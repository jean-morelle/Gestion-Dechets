<?php
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitoyenController;
use App\Http\Controllers\CollecteurController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;

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

// Pages légales (publiques)
Route::view('/confidentialite', 'legal.confidentialite')->name('legal.confidentialite');
Route::view('/conditions-utilisation', 'legal.conditions')->name('legal.conditions');

// Routes d'authentification
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
// Le contrôleur bloque 5 essais par compte ; cette limite protège contre les essais en rafale sur plusieurs comptes
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware(['guest', 'throttle:20,1']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Routes Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Routes collecteur
Route::middleware(['auth', 'role:collecteur'])->group(function () {
    Route::get('/collecteur/dashboard', [App\Http\Controllers\CollecteurController::class, 'dashboard'])->name('collecteur.dashboard');
    
    // Tournées : feuille de route, démarrage, passage à chaque étape, clôture
    Route::get('/collecteur/itineraires', [App\Http\Controllers\CollecteurController::class, 'consulterItineraires'])->name('collecteur.itineraires.index');
    Route::get('/collecteur/itineraires/{itineraire}', [App\Http\Controllers\CollecteurController::class, 'afficherItineraire'])->name('collecteur.itineraires.show');
    Route::post('/collecteur/itineraires/{itineraire}/demarrer', [App\Http\Controllers\CollecteurController::class, 'demarrerItineraire'])->name('collecteur.itineraires.demarrer');
    Route::post('/collecteur/itineraires/{itineraire}/terminer', [App\Http\Controllers\CollecteurController::class, 'terminerItineraire'])->name('collecteur.itineraires.terminer');
    Route::post('/collecteur/collectes/{collecte}/passage', [App\Http\Controllers\CollecteurController::class, 'validerPassage'])->name('collecteur.collectes.passage');
    Route::post('/collecteur/collectes/{collecte}/echec', [App\Http\Controllers\CollecteurController::class, 'signalerEchec'])->name('collecteur.collectes.echec');

    // Historique des collectes
    Route::get('/collecteur/collectes', [App\Http\Controllers\CollecteurController::class, 'indexCollectes'])->name('collecteur.collectes.index');
    Route::get('/collecteur/collectes/{collecte}', [App\Http\Controllers\CollecteurController::class, 'showCollecte'])->name('collecteur.collectes.show');

    // Incidents
    Route::get('/collecteur/incidents', [App\Http\Controllers\CollecteurController::class, 'indexIncidents'])->name('collecteur.incidents.index');
    Route::get('/collecteur/incidents/create', [App\Http\Controllers\CollecteurController::class, 'createIncident'])->name('collecteur.incidents.create');
    Route::post('/collecteur/incidents', [App\Http\Controllers\CollecteurController::class, 'storeIncident'])->name('collecteur.incidents.store');
    Route::get('/collecteur/incidents/{incident}', [App\Http\Controllers\CollecteurController::class, 'showIncident'])->name('collecteur.incidents.show');

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
    
    // Comptes : les citoyens s'inscrivent seuls, les agents sont créés ici
    Route::resource('/admin/utilisateurs', App\Http\Controllers\AdminUtilisateurController::class)
        ->except('edit')->parameters(['utilisateurs' => 'utilisateur'])->names('admin.utilisateurs');
    Route::post('/admin/utilisateurs/{utilisateur}/mot-de-passe', [App\Http\Controllers\AdminUtilisateurController::class, 'reinitialiserMotDePasse'])->name('admin.utilisateurs.reinitialiser');

    // Demandes de collecte des citoyens
    Route::get('/admin/demandes', [App\Http\Controllers\AdminDemandeController::class, 'index'])->name('admin.demandes.index');
    Route::get('/admin/demandes/{demande}', [App\Http\Controllers\AdminDemandeController::class, 'show'])->name('admin.demandes.show');
    Route::post('/admin/demandes/{demande}/accepter', [App\Http\Controllers\AdminDemandeController::class, 'accepter'])->name('admin.demandes.accepter');
    Route::post('/admin/demandes/{demande}/refuser', [App\Http\Controllers\AdminDemandeController::class, 'refuser'])->name('admin.demandes.refuser');
    Route::post('/admin/demandes/{demande}/terminer', [App\Http\Controllers\AdminDemandeController::class, 'terminer'])->name('admin.demandes.terminer');

    // Incidents signalés par les collecteurs
    Route::get('/admin/incidents', [App\Http\Controllers\AdminIncidentController::class, 'index'])->name('admin.incidents.index');
    Route::get('/admin/incidents/{incident}', [App\Http\Controllers\AdminIncidentController::class, 'show'])->name('admin.incidents.show');
    Route::put('/admin/incidents/{incident}', [App\Http\Controllers\AdminIncidentController::class, 'update'])->name('admin.incidents.update');
    
    // Routes signalements
    Route::get('/admin/signalements', [App\Http\Controllers\AdminSignalementController::class, 'index'])->name('admin.signalements.index');
    Route::get('/admin/signalements/{signalement}', [App\Http\Controllers\AdminSignalementController::class, 'show'])->name('admin.signalements.show');
    Route::put('/admin/signalements/{signalement}', [App\Http\Controllers\AdminSignalementController::class, 'update'])->name('admin.signalements.update');
    
    // Routes plaintes
    Route::get('/admin/plaintes', [App\Http\Controllers\AdminPlainteController::class, 'index'])->name('admin.plaintes.index');
    Route::get('/admin/plaintes/{plainte}', [App\Http\Controllers\AdminPlainteController::class, 'show'])->name('admin.plaintes.show');
    Route::put('/admin/plaintes/{plainte}', [App\Http\Controllers\AdminPlainteController::class, 'update'])->name('admin.plaintes.update');
    
    // Tournées (itinéraires) et points de collecte
    Route::resource('/admin/itineraires', App\Http\Controllers\AdminItineraireController::class)
        ->parameters(['itineraires' => 'itineraire'])->names('admin.itineraires');
    Route::resource('/admin/points', App\Http\Controllers\AdminPointCollecteController::class)
        ->except('show')->parameters(['points' => 'point'])->names('admin.points');
    
    // Routes calendrier
    Route::resource('/admin/calendrier', App\Http\Controllers\AdminCalendrierController::class)
        ->except('show')->parameters(['calendrier' => 'calendrier'])->names('admin.calendrier');
    
    // Carte de la commune et rapports d'activité
    Route::get('/admin/carte', [App\Http\Controllers\AdminCarteController::class, 'index'])->name('admin.carte');
    Route::get('/admin/rapports', [App\Http\Controllers\AdminRapportController::class, 'index'])->name('admin.rapports');
    Route::get('/admin/rapports/export/{jeu}', [App\Http\Controllers\AdminRapportController::class, 'exporter'])
        ->whereIn('jeu', ['signalements', 'demandes', 'plaintes', 'passages'])->name('admin.rapports.export');
    
    // Routes notifications admin
    Route::get('/admin/notifications', [App\Http\Controllers\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/create', [App\Http\Controllers\AdminNotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/admin/notifications', [App\Http\Controllers\AdminNotificationController::class, 'store'])->name('admin.notifications.store');
    Route::get('/admin/notifications/{notification}/ouvrir', [App\Http\Controllers\AdminNotificationController::class, 'ouvrir'])->name('admin.notifications.ouvrir');
    Route::post('/admin/notifications/tout-lu', [App\Http\Controllers\AdminNotificationController::class, 'toutMarquerLu'])->name('admin.notifications.tout-lu');
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

// Inscription et mot de passe oublié (visiteurs non connectés, envois limités contre les abus)
Route::middleware('guest')->group(function () {
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->middleware('throttle:5,10');

    Route::get('/mot-de-passe-oublie', [App\Http\Controllers\Auth\MotDePasseOublieController::class, 'demande'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [App\Http\Controllers\Auth\MotDePasseOublieController::class, 'envoyerLien'])->middleware('throttle:5,10')->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [App\Http\Controllers\Auth\MotDePasseOublieController::class, 'formulaire'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [App\Http\Controllers\Auth\MotDePasseOublieController::class, 'reinitialiser'])->middleware('throttle:10,10')->name('password.update');
});

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
    // Profil
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    // Paramètres
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance.update');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::get('/settings/mes-donnees', [SettingsController::class, 'exporterDonnees'])->name('settings.donnees');
    Route::delete('/settings/compte', [SettingsController::class, 'supprimerCompte'])->name('settings.compte.destroy');
    Route::put('/settings/mot-de-passe', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::delete('/settings/appareils', [SettingsController::class, 'destroyOtherSessions'])->name('settings.sessions.destroy');
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
    
    // Campagnes de sensibilisation
    Route::get('/campagnes', [CitoyenController::class, 'indexCampagnes'])->name('campagnes.index');
    Route::get('/campagnes/{campagne}', [CitoyenController::class, 'showCampagne'])->name('campagnes.show');
    Route::post('/campagnes/{campagne}/partager', [CitoyenController::class, 'partagerCampagne'])->name('campagnes.partager');
    
    // Notifications
    Route::get('/notifications', [CitoyenController::class, 'indexNotifications'])->name('notifications.index');
    Route::post('/notifications/{notificationId}/marquer-lue', [CitoyenController::class, 'marquerNotificationLue'])->name('notifications.marquer-lue');
    Route::post('/notifications/marquer-toutes-lues', [CitoyenController::class, 'marquerToutesNotificationsLues'])->name('notifications.marquer-toutes-lues');
    
    Route::get('/notifications/nombre-non-lues', [CitoyenController::class, 'nombreNotificationsNonLues'])->name('notifications.nombre-non-lues');
    
    // Profil
    Route::get('/profil', [CitoyenController::class, 'profil'])->name('profil');
    
    // Paramètres
});

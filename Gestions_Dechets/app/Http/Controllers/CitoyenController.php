<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Models\Plainte;
use App\Models\CalendrierCollecte;
use App\Models\Notification;
use App\Models\DemandeCollecte;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CitoyenController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tableau de bord du citoyen
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $statistiques = [
            'signalements' => [
                'total' => $user->signalements()->count(),
                'en_attente' => $user->signalements()->where('statut', 'en_attente')->count(),
                'traites' => $user->signalements()->where('statut', 'traite')->count(),
            ],
            'plaintes' => [
                'total' => $user->plaintes()->count(),
                'en_attente' => $user->plaintes()->where('statut', 'en_attente')->count(),
                'traitees' => $user->plaintes()->where('statut', 'traite')->count(),
            ],
            'demandes_collecte' => [
                'total' => $user->demandesCollecte()->count(),
                'en_attente' => $user->demandesCollecte()->where('statut', 'en_attente')->count(),
                'acceptees' => $user->demandesCollecte()->where('statut', 'accepte')->count(),
            ],
            'notifications' => [
                'non_lues' => $this->notificationService->compterNotificationsNonLues($user->id),
            ]
        ];

        $signalementsRecents = $user->signalements()->latest()->limit(5)->get();
        $plaintesRecentes = $user->plaintes()->latest()->limit(5)->get();
        $demandesRecentes = $user->demandesCollecte()->latest()->limit(5)->get();

        return view('citoyen.dashboard', compact('statistiques', 'signalementsRecents', 'plaintesRecentes', 'demandesRecentes'));
    }

    /**
     * Afficher le formulaire de signalement
     */
    public function creerSignalement()
    {
        return view('citoyen.signalements.create');
    }

    /**
     * Enregistrer un nouveau signalement
     */
    public function enregistrerSignalement(Request $request)
    {
        $request->validate([
            'type_dechet' => 'required|in:dechet_menager,dechet_vert,encombrant,dechet_dangereux,dechet_recyclable,autre',
            'description' => 'required|string|max:1000',
            'adresse' => 'required|string|max:255',
            'quartier' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Gérer l'upload de photo
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'signalements/' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public', $filename);
            $data['photo'] = $filename;
        }

        $signalement = Signalement::create($data);

        // Envoyer les notifications
        $this->notificationService->notifierNouveauSignalement($signalement);

        return redirect()->route('citoyen.signalements.show', $signalement)
                        ->with('success', 'Signalement enregistré avec succès !');
    }

    /**
     * Afficher un signalement
     */
    public function afficherSignalement(Signalement $signalement)
    {
        if ($signalement->user_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à ce signalement.');
        }
        
        return view('citoyen.signalements.show', compact('signalement'));
    }

    /**
     * Lister les signalements du citoyen
     */
    public function listerSignalements(Request $request)
    {
        $query = Auth::user()->signalements()->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_dechet')) {
            $query->where('type_dechet', $request->type_dechet);
        }

        $signalements = $query->paginate(5);

        return view('citoyen.signalements.index', compact('signalements'));
    }

    /**
     * Afficher le formulaire de demande de collecte
     */
    public function creerDemandeCollecte()
    {
        return view('citoyen.demandes-collecte.create');
    }

    /**
     * Enregistrer une nouvelle demande de collecte
     */
    public function enregistrerDemandeCollecte(Request $request)
    {
        $request->validate([
            'type_collecte' => 'required|in:menagere,encombrant,vert,recyclable,dangereux,demenagement',
            'objet' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'adresse' => 'required|string|max:255',
            'quartier' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'urgence' => 'required|in:faible,moyenne,elevee,urgente',
            'date_souhaitee' => 'nullable|date|after_or_equal:today',
            'heure_souhaitee' => 'nullable|date_format:H:i',
            'contact_telephone' => 'nullable|string|max:20',
            'instructions_speciales' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'montant_estime' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['contact_telephone'] = $request->get('contact_telephone') ?: Auth::user()->telephone;

        // Gérer l'upload de photo
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'demandes-collecte/' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public', $filename);
            $data['photo'] = $filename;
        }

        $demande = DemandeCollecte::create($data);

        // Envoyer les notifications (à implémenter plus tard)
        // $this->notificationService->notifierNouvelleDemandeCollecte($demande);

        return redirect()->route('citoyen.demandes-collecte.show', $demande)
                        ->with('success', 'Demande de collecte soumise avec succès !');
    }

    /**
     * Afficher une demande de collecte
     */
    public function afficherDemandeCollecte(DemandeCollecte $demandeCollecte)
    {
        // Vérifier que la demande appartient à l'utilisateur connecté
        if ($demandeCollecte->user_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cette demande.');
        }
        
        return view('citoyen.demandes-collecte.show', compact('demandeCollecte'));
    }

    /**
     * Lister les demandes de collecte du citoyen
     */
    public function listerDemandesCollecte(Request $request)
    {
        $query = Auth::user()->demandesCollecte()->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_collecte')) {
            $query->where('type_collecte', $request->type_collecte);
        }

        if ($request->filled('urgence')) {
            $query->where('urgence', $request->urgence);
        }

        $demandesCollecte = $query->paginate(5);

        return view('citoyen.demandes-collecte.index', compact('demandesCollecte'));
    }

    /**
     * Consulter le calendrier de collecte
     */
    public function consulterCalendrier(Request $request)
    {
        $query = CalendrierCollecte::where('statut', 'actif');

        // Filtres
        if ($request->filled('quartier')) {
            $query->where('quartier', $request->quartier);
        }

        if ($request->filled('type_collecte')) {
            $query->where('type_collecte', $request->type_collecte);
        }

        $collectes = $query->paginate(5);
        $quartiers = CalendrierCollecte::distinct()->pluck('quartier')->sort();
        
        // Générer les dates de collecte pour le calendrier visuel
        $collectionDates = $this->generateCollectionDates($query->get(), $request->get('month', date('Y-m')));
        
        // Données d'exemple si aucune collecte n'est trouvée
        if ($collectes->isEmpty()) {
            $prochaines_collectes = collect([]);
        } else {
            $prochaines_collectes = $collectes->take(5);
        }

        // Si c'est une requête AJAX, retourner les données en JSON
        if ($request->ajax()) {
            return response()->json([
                'collectionDates' => $collectionDates,
                'collectes' => $collectes,
                'quartiers' => $quartiers
            ]);
        }

        return view('citoyen.calendrier.index', compact('collectes', 'quartiers', 'prochaines_collectes', 'collectionDates'));
    }

    /**
     * Générer les dates de collecte pour un mois donné
     */
    private function generateCollectionDates($calendriers, $month)
    {
        $dates = [];
        $currentMonth = \Carbon\Carbon::parse($month);
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        foreach ($calendriers as $calendrier) {
            if (!$calendrier->isActif()) {
                continue;
            }

            switch ($calendrier->frequence) {
                case 'quotidienne':
                    // Tous les jours du mois
                    $current = $startOfMonth->copy();
                    while ($current->lte($endOfMonth)) {
                        if ($calendrier->isDateValide($current->toDateString())) {
                            $dates[] = $current->day;
                        }
                        $current->addDay();
                    }
                    break;

                case 'hebdomadaire':
                    if ($calendrier->jour_semaine) {
                        $jourSemaine = match($calendrier->jour_semaine) {
                            'lundi' => 1,
                            'mardi' => 2,
                            'mercredi' => 3,
                            'jeudi' => 4,
                            'vendredi' => 5,
                            'samedi' => 6,
                            'dimanche' => 0,
                            default => null
                        };
                        
                        if ($jourSemaine !== null) {
                            $current = $startOfMonth->copy()->next($jourSemaine);
                            while ($current->lte($endOfMonth)) {
                                if ($calendrier->isDateValide($current->toDateString())) {
                                    $dates[] = $current->day;
                                }
                                $current->addWeek();
                            }
                        }
                    }
                    break;

                case 'mensuelle':
                    // Le premier jour du mois
                    if ($calendrier->isDateValide($startOfMonth->toDateString())) {
                        $dates[] = 1;
                    }
                    break;

                case 'ponctuelle':
                    if ($calendrier->date_debut && 
                        $calendrier->date_debut->month == $currentMonth->month && 
                        $calendrier->date_debut->year == $currentMonth->year) {
                        $dates[] = $calendrier->date_debut->day;
                    }
                    break;
            }
        }

        return array_unique($dates);
    }


    /**
     * Gérer les notifications
     */
    public function notifications(Request $request)
    {
        $query = Auth::user()->notifications()->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(15);

        return view('citoyen.notifications.index', compact('notifications'));
    }


    /**
     * Obtenir le nombre de notifications non lues (AJAX)
     */
    public function nombreNotificationsNonLues()
    {
        $count = $this->notificationService->compterNotificationsNonLues(Auth::id());

        return response()->json(['count' => $count]);
    }

    /**
     * Afficher les notifications du citoyen
     */
    public function indexNotifications()
    {
        $notifications = \App\Models\Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('citoyen.notifications.index', compact('notifications'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerNotificationLue($notificationId)
    {
        $notification = \App\Models\Notification::where('user_id', Auth::id())
            ->findOrFail($notificationId);
        $notification->marquerCommeLue();

        return response()->json(['success' => true], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function marquerToutesNotificationsLues()
    {
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('statut', \App\Models\Notification::STATUT_NON_LU)
            ->update([
                'statut' => \App\Models\Notification::STATUT_LU,
                'date_lecture' => now()
            ]);

        return response()->json(['success' => true], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Afficher les campagnes pour les citoyens
     */
    public function indexCampagnes()
    {
        $campagnes = \App\Models\Campagne::visible()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('citoyen.campagnes.index', compact('campagnes'));
    }

    /**
     * Afficher les détails d'une campagne pour les citoyens
     */
    public function showCampagne(\App\Models\Campagne $campagne)
    {
        // Incrémenter le nombre de vues
        $campagne->incrementerVues();

        return view('citoyen.campagnes.show', compact('campagne'));
    }

    /**
     * Partager une campagne (citoyen)
     */
    public function partagerCampagne(\App\Models\Campagne $campagne)
    {
        // Incrémenter le nombre de partages
        $campagne->incrementerPartages();

        return redirect()->back()
            ->with('success', 'Campagne partagée avec succès !');
    }

    /**
     * Afficher le formulaire de plainte
     */
    public function creerPlainte()
    {
        return view('citoyen.plaintes.create');
    }

    /**
     * Enregistrer une nouvelle plainte
     */
    public function enregistrerPlainte(Request $request)
    {
        $request->validate([
            'type_plainte' => 'required|in:collecte_retard,collecte_oubliee,service_client,autre',
            'sujet' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'adresse' => 'required|string|max:255',
            'quartier' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_telephone' => 'nullable|string|max:20',
            'priorite' => 'required|in:faible,moyenne,elevee,urgente',
            'signalement_id' => 'nullable|exists:signalements,id',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Gérer l'upload de photo
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'plaintes/' . \Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public', $filename);
            $data['photo'] = $filename;
        }

        $plainte = Plainte::create($data);

        return redirect()->route('citoyen.plaintes.show', $plainte)
                        ->with('success', 'Plainte enregistrée avec succès !');
    }

    /**
     * Afficher une plainte
     */
    public function afficherPlainte(Plainte $plainte)
    {
        if ($plainte->user_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cette plainte.');
        }
        
        return view('citoyen.plaintes.show', compact('plainte'));
    }

    /**
     * Lister les plaintes du citoyen
     */
    public function listerPlaintes(Request $request)
    {
        $query = Auth::user()->plaintes()->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_plainte')) {
            $query->where('type_plainte', $request->type_plainte);
        }

        $plaintes = $query->paginate(5);

        return view('citoyen.plaintes.index', compact('plaintes'));
    }

    /**
     * Profil du citoyen
     */
    public function profil()
    {
        $user = Auth::user();
        
        return view('citoyen.profil', compact('user'));
    }
}


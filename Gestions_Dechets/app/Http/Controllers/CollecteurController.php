<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use App\Models\Incident;
use App\Models\Itineraire;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CollecteurController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tableau de bord du collecteur
     */
    public function dashboard()
    {
        $user = Auth::user();

        $statistiques = [
            'itineraires' => [
                'total' => $user->itineraires()->count(),
                'en_cours' => $user->itineraires()->where('statut', 'en_cours')->count(),
                'termines' => $user->itineraires()->where('statut', 'termine')->count(),
            ],
            'collectes' => [
                'total' => $user->collectes()->count(),
                'en_cours' => $user->collectes()->where('statut', Collecte::STATUT_PREVUE)
                    ->whereHas('itineraire', fn ($q) => $q->where('statut', Itineraire::STATUT_EN_COURS))->count(),
                'terminees' => $user->collectes()->where('statut', 'termine')->count(),
                'ratees' => $user->collectes()->where('statut', 'rate')->count(),
            ],
            'notifications' => [
                'non_lues' => $this->notificationService->compterNotificationsNonLues($user->id),
            ]
        ];

        // Tournées à faire en priorité : celle en cours d'abord, puis les prochaines planifiées
        $itinerairesRecents = $user->itineraires()
            ->whereIn('statut', ['en_cours', 'planifie'])
            ->orderByRaw("CASE WHEN statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderBy('date_debut')
            ->limit(5)
            ->get();
        $collectesRecentes = $user->collectes()->with('pointDeCollecte')->latest()->limit(5)->get();
        $collectesEnCours = $user->collectes()->where('statut', 'en_cours')
                                      ->with(['pointDeCollecte', 'itineraire'])
                                      ->latest()
                                      ->get();

        $campagnesRecentes = \App\Models\Campagne::visible()
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('collecteur.dashboard', compact('statistiques', 'itinerairesRecents', 'collectesRecentes', 'collectesEnCours', 'campagnesRecentes'));
    }

    /**
     * Tournées confiées au collecteur
     */
    public function consulterItineraires(Request $request)
    {
        $query = Auth::user()->itineraires()
            ->with('collectes')
            ->withCount('pointsDeCollecte')
            ->orderByRaw("CASE statut WHEN 'en_cours' THEN 0 WHEN 'planifie' THEN 1 ELSE 2 END")
            ->orderBy('date_debut');

        // Onglet « À faire » par défaut, « Terminées » sur demande
        if ($request->query('statut') === 'termine') {
            $query->whereIn('statut', [Itineraire::STATUT_TERMINE, Itineraire::STATUT_ANNULE])->reorder()->orderByDesc('date_debut');
        } else {
            $query->whereIn('statut', [Itineraire::STATUT_PLANIFIE, Itineraire::STATUT_EN_COURS]);
        }

        $itineraires = $query->paginate(12)->withQueryString();

        return view('collecteur.itineraires.index', compact('itineraires'));
    }

    /**
     * Feuille de route d'une tournée : étapes dans l'ordre, carte et actions
     */
    public function afficherItineraire(Itineraire $itineraire)
    {
        $this->verifierTournee($itineraire);

        $itineraire->load(['pointsDeCollecte', 'collectes']);
        $collectesParPoint = $itineraire->collectes->keyBy('point_collecte_id');

        return view('collecteur.itineraires.show', compact('itineraire', 'collectesParPoint'));
    }

    /**
     * Démarrer la tournée : une collecte « à faire » est créée pour chaque étape
     */
    public function demarrerItineraire(Itineraire $itineraire)
    {
        $this->verifierTournee($itineraire);

        if ($itineraire->statut !== Itineraire::STATUT_PLANIFIE) {
            return back()->with('error', 'Cette tournée a déjà été démarrée.');
        }
        if (! $itineraire->pointsDeCollecte()->exists()) {
            return back()->with('error', 'Cette tournée ne contient aucune étape. Contactez l’administration.');
        }
        if (Auth::user()->itineraires()->where('statut', Itineraire::STATUT_EN_COURS)->exists()) {
            return back()->with('error', 'Terminez d’abord la tournée en cours avant d’en démarrer une autre.');
        }

        $itineraire->demarrer();

        $this->notifierAdmin($itineraire, 'Tournée démarrée', Auth::user()->name . ' a démarré la tournée « ' . $itineraire->nom . ' ».');

        return redirect()->route('collecteur.itineraires.show', $itineraire)->with('success', 'Tournée démarrée. Bonne collecte !');
    }

    /**
     * Clôturer la tournée (les étapes restantes sont marquées non collectées)
     */
    public function terminerItineraire(Itineraire $itineraire)
    {
        $this->verifierTournee($itineraire);

        if ($itineraire->statut !== Itineraire::STATUT_EN_COURS) {
            return back()->with('error', 'Cette tournée n’est pas en cours.');
        }

        $itineraire->terminer();
        $p = $itineraire->fresh()->progression;

        $this->notifierAdmin(
            $itineraire,
            'Tournée terminée',
            Auth::user()->name . " a terminé « {$itineraire->nom} » : {$p['collectees']} étape(s) collectée(s) sur {$p['total']}."
        );

        return redirect()->route('collecteur.itineraires.show', $itineraire)->with('success', 'Tournée terminée. Merci !');
    }

    /**
     * Valider le passage à une étape : photo obligatoire, position GPS si disponible
     */
    public function validerPassage(Request $request, Collecte $collecte)
    {
        $this->verifierEtapeModifiable($collecte);

        $donnees = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'type_dechet' => ['required', Rule::in(array_keys(Collecte::TYPES_DECHET))],
            'quantite' => ['required', 'numeric', 'min:0', 'max:50000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'precision' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'photo.required' => 'Prenez une photo du point après le ramassage.',
            'quantite.required' => 'Indiquez une estimation de la quantité ramassée.',
        ], [
            'quantite' => 'quantité',
            'type_dechet' => 'type de déchet',
        ]);

        $distance = null;
        if (isset($donnees['latitude'], $donnees['longitude'])) {
            $distance = (int) round($collecte->pointDeCollecte->distanceMetres($donnees['latitude'], $donnees['longitude']));
        }

        $collecte->update([
            'statut' => Collecte::STATUT_TERMINE,
            'heure_fin' => now(),
            'type_dechet' => $donnees['type_dechet'],
            'quantite' => $donnees['quantite'],
            'latitude_fin' => $donnees['latitude'] ?? null,
            'longitude_fin' => $donnees['longitude'] ?? null,
            'precision_gps' => isset($donnees['precision']) ? (int) round($donnees['precision']) : null,
            'distance_point' => $distance,
            'validation_gps' => $distance !== null && $distance <= Collecte::TOLERANCE_GPS_METRES,
            'photo_validation' => $request->file('photo')->store('collectes', 'public'),
            'notes' => $donnees['notes'] ?? null,
        ]);

        return $this->retourTournee($collecte, 'Passage enregistré à « ' . $collecte->pointDeCollecte->nom . ' ».');
    }

    /**
     * Signaler qu'une étape n'a pas pu être collectée
     */
    public function signalerEchec(Request $request, Collecte $collecte)
    {
        $this->verifierEtapeModifiable($collecte);

        $donnees = $request->validate([
            'motif_echec' => ['required', Rule::in(array_keys(Collecte::MOTIFS_ECHEC))],
            'notes' => ['nullable', 'required_if:motif_echec,autre', 'string', 'max:1000'],
        ], [
            'motif_echec.required' => 'Choisissez la raison.',
            'notes.required_if' => 'Précisez la raison.',
        ]);

        $collecte->update([
            'statut' => Collecte::STATUT_RATE,
            'heure_fin' => now(),
            'motif_echec' => $donnees['motif_echec'],
            'notes' => $donnees['notes'] ?? null,
        ]);

        return $this->retourTournee($collecte, 'Étape « ' . $collecte->pointDeCollecte->nom . ' » marquée non collectée.');
    }

    /**
     * Détail d'une collecte
     */
    public function showCollecte(Collecte $collecte)
    {
        abort_unless($collecte->collecteur_id === Auth::id(), 403, 'Vous n’avez pas accès à cette collecte.');

        $collecte->load(['pointDeCollecte', 'itineraire']);

        return view('collecteur.collectes.show', compact('collecte'));
    }

    /**
     * Historique des collectes du collecteur
     */
    public function indexCollectes(Request $request)
    {
        $query = Auth::user()->collectes()
            ->with(['pointDeCollecte', 'itineraire'])
            ->whereIn('statut', [Collecte::STATUT_TERMINE, Collecte::STATUT_RATE]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date_collecte', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_collecte', '<=', $request->date_fin);
        }

        $collectes = $query->latest('heure_fin')->paginate(20)->withQueryString();

        return view('collecteur.collectes.index', compact('collectes'));
    }

    /**
     * Formulaire de signalement d'incident
     */
    public function createIncident(Request $request)
    {
        $itineraires = Auth::user()->itineraires()
            ->whereIn('statut', [Itineraire::STATUT_EN_COURS, Itineraire::STATUT_PLANIFIE])
            ->orderByRaw("CASE WHEN statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderBy('date_debut')
            ->get();

        $selection = (int) $request->query('itineraire_id', $itineraires->first()?->id);

        return view('collecteur.incidents.create', compact('itineraires', 'selection'));
    }

    /**
     * Enregistrer un incident
     */
    public function storeIncident(Request $request)
    {
        $donnees = $request->validate([
            'itineraire_id' => ['required', 'integer'],
            'type_incident' => ['required', Rule::in(['panne_vehicule', 'probleme_acces', 'dechet_non_collectable', 'autre'])],
            'description' => ['required', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ], [], [
            'itineraire_id' => 'tournée',
            'type_incident' => 'type d’incident',
        ]);

        $itineraire = Itineraire::findOrFail($donnees['itineraire_id']);
        $this->verifierTournee($itineraire);

        $incident = Incident::create([
            'collecteur_id' => Auth::id(),
            'itineraire_id' => $itineraire->id,
            'type_incident' => $donnees['type_incident'],
            'description' => $donnees['description'],
            'latitude' => $donnees['latitude'] ?? null,
            'longitude' => $donnees['longitude'] ?? null,
            'photo' => $request->hasFile('photo') ? $request->file('photo')->store('incidents', 'public') : null,
            'statut' => 'signale',
            'priorite' => $donnees['type_incident'] === 'panne_vehicule' ? 'elevee' : 'normale',
        ]);

        $this->notifierAdmin(
            $itineraire,
            'Incident : ' . $incident->type_label,
            Auth::user()->name . ' signale un incident sur « ' . $itineraire->nom . ' » : ' . $incident->description
        );

        return redirect()->route('collecteur.itineraires.show', $itineraire)
            ->with('success', 'Incident transmis à l’administration.');
    }

    /**
     * Incidents signalés par le collecteur
     */
    public function indexIncidents()
    {
        $incidents = Auth::user()->incidents()->with('itineraire')->latest()->paginate(10);

        return view('collecteur.incidents.index', compact('incidents'));
    }

    /**
     * Détail d'un incident
     */
    public function showIncident(Incident $incident)
    {
        abort_unless($incident->collecteur_id === Auth::id(), 403, 'Vous n’avez pas accès à cet incident.');

        $incident->load('itineraire');

        return view('collecteur.incidents.show', compact('incident'));
    }

    private function verifierTournee(Itineraire $itineraire): void
    {
        abort_unless($itineraire->collecteur_id === Auth::id(), 403, 'Cette tournée ne vous est pas attribuée.');
    }

    private function verifierEtapeModifiable(Collecte $collecte): void
    {
        abort_unless($collecte->collecteur_id === Auth::id(), 403, 'Vous n’avez pas accès à cette collecte.');

        if ($collecte->itineraire->statut !== Itineraire::STATUT_EN_COURS || $collecte->estTraitee()) {
            abort(redirect()->route('collecteur.itineraires.show', $collecte->itineraire_id)
                ->with('error', 'Cette étape a déjà été traitée ou la tournée n’est plus en cours.'));
        }
    }

    private function retourTournee(Collecte $collecte, string $message)
    {
        $restantes = $collecte->itineraire->progression['restantes'];
        if ($restantes === 0) {
            $message .= ' Toutes les étapes sont faites : vous pouvez terminer la tournée.';
        }

        return redirect()->to(route('collecteur.itineraires.show', $collecte->itineraire_id) . '#etape-' . $collecte->id)
            ->with('success', $message);
    }

    private function notifierAdmin(Itineraire $itineraire, string $titre, string $message): void
    {
        if (! $itineraire->admin_id) {
            return;
        }

        $this->notificationService->creerNotification(
            $itineraire->admin_id,
            'itineraire',
            $titre,
            $message,
            route('admin.itineraires.show', $itineraire)
        );
    }

    /**
     * Afficher les notifications du collecteur
     */
    public function indexNotifications()
    {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('collecteur.notifications.index', compact('notifications'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerNotificationLue($notificationId)
    {
        $notification = \App\Models\Notification::where('user_id', auth()->id())
            ->findOrFail($notificationId);
        $notification->marquerCommeLue();

        return response()->json(['success' => true], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function marquerToutesNotificationsLues()
    {
        \App\Models\Notification::where('user_id', auth()->id())
            ->where('statut', \App\Models\Notification::STATUT_NON_LU)
            ->update([
                'statut' => \App\Models\Notification::STATUT_LU,
                'date_lecture' => now()
            ]);

        return response()->json(['success' => true], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Afficher les campagnes disponibles pour les collecteurs
     */
    public function indexCampagnes()
    {
        // Afficher toutes les campagnes visibles, comme pour les citoyens
        $campagnes = \App\Models\Campagne::visible()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Statistiques
        $stats = [
            'total' => \App\Models\Campagne::visible()->count(),
            'affiches' => \App\Models\Campagne::visible()->parType('affiche')->count(),
            'videos' => \App\Models\Campagne::visible()->parType('video')->count(),
            'messages' => \App\Models\Campagne::visible()->parType('message')->count(),
            'infographies' => \App\Models\Campagne::visible()->parType('infographie')->count(),
        ];

        return view('collecteur.campagnes.index', compact('campagnes', 'stats'));
    }

    /**
     * Afficher les détails d'une campagne pour les collecteurs
     */
    public function showCampagne(\App\Models\Campagne $campagne)
    {
        // Incrémenter le nombre de vues
        $campagne->incrementerVues();

        return view('collecteur.campagnes.show', compact('campagne'));
    }

    /**
     * Partager une campagne avec les citoyens
     */
    public function partagerCampagne(\App\Models\Campagne $campagne)
    {
        // Incrémenter le nombre de partages
        $campagne->incrementerPartages();

        // Notifier tous les citoyens de la campagne partagée
        $citoyens = \App\Models\User::where('role', 'citoyen')->get();
        
        foreach ($citoyens as $citoyen) {
            \App\Models\Notification::create([
                'user_id' => $citoyen->id,
                'expediteur_id' => Auth::id(),
                'type' => \App\Models\Notification::TYPE_SYSTEME,
                'titre' => 'Nouvelle campagne de sensibilisation',
                'message' => "Le collecteur " . Auth::user()->name . " a partagé une nouvelle campagne : '{$campagne->titre}'. Découvrez-la maintenant !",
                'priorite' => \App\Models\Notification::PRIORITE_MOYENNE,
                'lien_action' => route('citoyen.campagnes.show', $campagne),
                'icone' => 'fas fa-bullhorn',
                'date_envoi' => now(),
                'statut' => \App\Models\Notification::STATUT_NON_LU,
                'data' => [
                    'campagne_id' => $campagne->id,
                    'type' => 'campagne_partagee',
                    'collecteur_id' => Auth::id()
                ]
            ]);
        }

        return redirect()->back()
            ->with('success', 'Campagne partagée avec succès ! Les citoyens ont été notifiés.');
    }

    /**
     * Obtenir le lien de partage mobile pour une campagne
     */
    public function partageMobile(\App\Models\Campagne $campagne)
    {
        return view('collecteur.campagnes.partage-mobile', compact('campagne'));
    }

}


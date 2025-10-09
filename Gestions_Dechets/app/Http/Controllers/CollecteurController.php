<?php

namespace App\Http\Controllers;

use App\Models\Itineraire;
use App\Models\Collecte;
use App\Models\PointDeCollecte;
use App\Models\Incident;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
                'en_cours' => $user->collectes()->where('statut', 'en_cours')->count(),
                'terminees' => $user->collectes()->where('statut', 'termine')->count(),
                'ratees' => $user->collectes()->where('statut', 'rate')->count(),
            ],
            'notifications' => [
                'non_lues' => $this->notificationService->compterNotificationsNonLues($user->id),
            ]
        ];

        $itinerairesRecents = $user->itineraires()->latest()->limit(5)->get();
        $collectesRecentes = $user->collectes()->latest()->limit(5)->get();
        $collectesEnCours = $user->collectes()->where('statut', 'en_cours')
                                      ->with(['pointDeCollecte', 'itineraire'])
                                      ->latest()
                                      ->get();

        // Récupérer les campagnes récentes
        $campagnesRecentes = \App\Models\Campagne::visible()
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('collecteur.dashboard', compact('statistiques', 'itinerairesRecents', 'collectesRecentes', 'collectesEnCours', 'campagnesRecentes'));
    }

    /**
     * Consulter les itinéraires du collecteur
     */
    public function consulterItineraires(Request $request)
    {
        $query = Auth::user()->itineraires()->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('date_debut', $request->date);
        }

        $itineraires = $query->paginate(10);

        return view('collecteur.itineraires.index', compact('itineraires'));
    }

    /**
     * Afficher un itinéraire
     */
    public function afficherItineraire(Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient au collecteur connecté
        if ($itineraire->collecteur_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cet itinéraire.');
        }
        
        // Ordre de passage cohérent: par ordre du pivot itineraire_points
        $pointsCollecte = $itineraire->pointsDeCollecte()
            ->orderBy('itineraire_points.ordre')
            ->get();

        // Trier les collectes selon l'ordre des points de l'itinéraire
        $collectes = \App\Models\Collecte::query()
            ->with('pointDeCollecte')
            ->join('itineraire_points', 'collectes.point_collecte_id', '=', 'itineraire_points.point_de_collecte_id')
            ->where('collectes.itineraire_id', $itineraire->id)
            ->where('itineraire_points.itineraire_id', $itineraire->id)
            ->orderBy('itineraire_points.ordre')
            ->select('collectes.*')
            ->get();

        return view('collecteur.itineraires.show', compact('itineraire', 'pointsCollecte', 'collectes'));
    }

    /**
     * Démarrer un itinéraire
     */
    public function demarrerItineraire(Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient au collecteur connecté
        if ($itineraire->collecteur_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cet itinéraire.');
        }
        
        if ($itineraire->statut !== 'planifie') {
            return back()->with('error', 'Cet itinéraire ne peut pas être démarré.');
        }

        $itineraire->demarrer();

        // Auto-créer des collectes "en_attente" pour chaque point sans collecte existante
        $points = $itineraire->pointsDeCollecte()->orderBy('itineraire_points.ordre')->get();
        foreach ($points as $point) {
            $existe = Collecte::where('itineraire_id', $itineraire->id)
                ->where('point_collecte_id', $point->id)
                ->exists();
            if (!$existe) {
                Collecte::create([
                    'itineraire_id' => $itineraire->id,
                    'point_collecte_id' => $point->id,
                    'collecteur_id' => Auth::id(),
                    'statut' => 'prevue',
                    'notes' => 'Créée lors du démarrage de l\'itinéraire'
                ]);
            }
        }

        return redirect()->route('collecteur.itineraires.show', $itineraire)
                        ->with('success', 'Itinéraire démarré avec succès !');
    }

    /**
     * Terminer un itinéraire
     */
    public function terminerItineraire(Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient au collecteur connecté
        if ($itineraire->collecteur_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cet itinéraire.');
        }
        
        if ($itineraire->statut !== 'en_cours') {
            return back()->with('error', 'Cet itinéraire n\'est pas en cours.');
        }

        $itineraire->terminer();

        return redirect()->route('collecteur.itineraires.show', $itineraire)
                        ->with('success', 'Itinéraire terminé avec succès !');
    }

    /**
     * Mettre à jour l'état de collecte
     */
    public function mettreAJourCollecte(Request $request, Collecte $collecte)
    {
        $this->authorize('update', $collecte);
        
        $request->validate([
            'statut' => 'required|in:en_cours,termine,rate',
            'quantite' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'incidents' => 'nullable|string|max:1000',
            'photo_apres' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['statut', 'quantite', 'notes']);
        
        if ($request->statut === 'termine') {
            $data['heure_fin'] = now();
            
            if ($collecte->heure_debut) {
                $data['temps_collecte'] = now()->diffInMinutes($collecte->heure_debut);
            }
        }

        $collecte->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Collecte mise à jour avec succès'
        ]);
    }

    /**
     * Valider une collecte avec GPS et photo
     */
    public function validerCollecte(Request $request, Collecte $collecte)
    {
        $this->authorize('update', $collecte);
        
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo_validation' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photo = $request->file('photo_validation');
        $filename = 'collectes/validation/' . $collecte->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
        $photo->storeAs('public', $filename);

        $collecte->validerCollecte(
            $request->latitude,
            $request->longitude,
            $filename
        );

        return response()->json([
            'success' => true,
            'message' => 'Collecte validée avec succès'
        ]);
    }

    /**
     * Afficher un itinéraire spécifique
     */
    public function showItineraire(Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient au collecteur
        if ($itineraire->collecteur_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cet itinéraire.');
        }

        // Calculer la progression
        $totalPoints = $itineraire->pointsDeCollecte->count();
        $collectesTerminees = $itineraire->collectes()->where('statut', 'termine')->count();
        $progression = $totalPoints > 0 ? round(($collectesTerminees / $totalPoints) * 100) : 0;

        return view('collecteur.itineraires.show', compact('itineraire', 'progression'));
    }

    /**
     * Démarrer une collecte
     */
    public function startCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'point_id' => 'required|exists:point_de_collectes,id',
            'itineraire_id' => 'required|exists:itineraires,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $collecteur = Auth::user();
        $point = PointDeCollecte::findOrFail($request->point_id);
        $itineraire = Itineraire::findOrFail($request->itineraire_id);

        // Vérifier que l'itinéraire appartient au collecteur
        if ($itineraire->collecteur_id !== $collecteur->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à cet itinéraire.'
            ], 403);
        }

        // Vérifier qu'il n'y a pas déjà une collecte en cours pour ce point
        $existingCollecte = Collecte::where('point_de_collecte_id', $point->id)
                                  ->where('itineraire_id', $itineraire->id)
                                  ->where('statut', 'en_cours')
                                  ->first();

        if ($existingCollecte) {
            return response()->json([
                'success' => false,
                'message' => 'Une collecte est déjà en cours pour ce point.'
            ], 400);
        }

        // Créer la collecte
        $collecte = Collecte::create([
            'itineraire_id' => $itineraire->id,
            'point_de_collecte_id' => $point->id,
            'collecteur_id' => $collecteur->id,
            'date_heure_debut' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'statut' => 'en_cours',
            'notes' => 'Collecte démarrée automatiquement'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collecte démarrée avec succès',
            'collecte_id' => $collecte->id
        ]);
    }

    /**
     * Valider une collecte avec GPS et photo
     */
    public function validateCollection(Request $request, Collecte $collecte)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'quantite' => 'required|numeric|min:0',
            'type_dechet_collecte' => 'required|string',
            'photo' => 'required|image|max:2048', // Max 2MB
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Vérifier que la collecte appartient au collecteur
        if ($collecte->collecteur_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cette collecte.');
        }

        // Vérifier que la collecte est en cours
        if ($collecte->statut !== 'en_cours') {
            return back()->withErrors(['collecte' => 'Cette collecte n\'est pas en cours.']);
        }

        // Sauvegarder la photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('collectes', 'public');
        }

        // Mettre à jour la collecte
        $collecte->update([
            'date_heure_fin' => now(),
            'quantite_collectee' => $request->quantite,
            'type_dechet_collecte' => $request->type_dechet_collecte,
            'statut' => 'termine',
            'preuve_photo' => $photoPath,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => $request->notes
        ]);

        // Vérifier si tous les points de l'itinéraire sont terminés
        $itineraire = $collecte->itineraire;
        $totalPoints = $itineraire->pointsDeCollecte->count();
        $collectesTerminees = $itineraire->collectes()->where('statut', 'termine')->count();

        if ($collectesTerminees >= $totalPoints) {
            $itineraire->update(['statut' => 'termine']);
            
            // Notifier l'administrateur
            $this->notificationService->creerNotification(
                $itineraire->admin_id,
                'info',
                "L'itinéraire '{$itineraire->nom}' a été terminé par {$collecteur->name}.",
                route('admin.itineraires.show', $itineraire)
            );
        }

        return redirect()->route('collecteur.itineraires.show', $itineraire)
                        ->with('success', 'Collecte validée avec succès !');
    }

    /**
     * Terminer un itinéraire
     */
    public function finishItinerary(Request $request, Itineraire $itineraire)
    {
        // Vérifier que l'itinéraire appartient au collecteur
        if ($itineraire->collecteur_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à cet itinéraire.'
            ], 403);
        }

        // Vérifier que l'itinéraire est en cours
        if ($itineraire->statut !== 'en_cours') {
            return response()->json([
                'success' => false,
                'message' => 'Cet itinéraire n\'est pas en cours.'
            ], 400);
        }

        // Terminer toutes les collectes en cours
        $itineraire->collectes()->where('statut', 'en_cours')->update([
            'statut' => 'annule',
            'notes' => 'Annulé lors de la fin d\'itinéraire'
        ]);

        // Marquer l'itinéraire comme terminé
        $itineraire->update(['statut' => 'termine']);

        // Notifier l'administrateur
        $collecteur = Auth::user();
        $this->notificationService->creerNotification(
            $itineraire->admin_id,
            'info',
            "L'itinéraire '{$itineraire->nom}' a été terminé par {$collecteur->name}.",
            route('admin.itineraires.show', $itineraire)
        );

        return response()->json([
            'success' => true,
            'message' => 'Itinéraire terminé avec succès'
        ]);
    }

    /**
     * Signaler un incident
     */
    public function createIncident()
    {
        $collecteur = Auth::user();
        $itinerairesActifs = $collecteur->itineraires()->where('statut', 'en_cours')->get();
        
        return view('collecteur.incidents.create', compact('itinerairesActifs'));
    }

    /**
     * Enregistrer un incident
     */
    public function storeIncident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'itineraire_id' => 'required|exists:itineraires,id',
            'type_incident' => 'required|string|in:panne_vehicule,probleme_acces,dechet_non_collectable,autre',
            'description' => 'required|string|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $itineraire = Itineraire::findOrFail($request->itineraire_id);

        // Vérifier que l'itinéraire appartient au collecteur
        if ($itineraire->collecteur_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cet itinéraire.');
        }

        // Sauvegarder la photo si fournie
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('incidents', 'public');
        }

        // Créer l'incident
        $incident = Incident::create([
            'collecteur_id' => Auth::id(),
            'itineraire_id' => $itineraire->id,
            'type_incident' => $request->type_incident,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $photoPath,
            'statut' => 'signale',
            'priorite' => 'normale'
        ]);

        // Notifier l'administrateur
        $collecteur = Auth::user();
        $this->notificationService->creerNotification(
            $itineraire->admin_id,
            'warning',
            "Incident signalé par {$collecteur->name} sur l'itinéraire '{$itineraire->nom}'.",
            route('admin.incidents.show', $incident)
        );

        return redirect()->route('collecteur.dashboard')
                        ->with('success', 'Incident signalé avec succès !');
    }

    /**
     * Afficher les incidents du collecteur
     */
    public function indexIncidents()
    {
        $collecteur = Auth::user();
        $incidents = $collecteur->incidents()->with('itineraire')->latest()->paginate(10);

        return view('collecteur.incidents.index', compact('incidents'));
    }

    /**
     * Afficher les détails d'un incident
     */
    public function showIncident(Request $request, Incident $incident)
    {
        // Vérifier que l'incident appartient au collecteur
        if ($incident->collecteur_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à cet incident.'
            ], 403);
        }

        $incident->load('itineraire');

        if ($request->expectsJson()) {
            $html = view('collecteur.incidents.partials.details', compact('incident'))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }

        return view('collecteur.incidents.show', compact('incident'));
    }

    /**
     * Afficher les détails d'une collecte
     */
    public function showCollecte(Request $request, Collecte $collecte)
    {
        // Vérifier que la collecte appartient au collecteur
        if ($collecte->collecteur_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à cette collecte.'
            ], 403);
        }

        $collecte->load(['pointDeCollecte', 'itineraire']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'collecte' => $collecte
            ]);
        }

        return view('collecteur.collectes.show', compact('collecte'));
    }

    /**
     * Mettre à jour le statut d'une collecte
     */
    public function updateCollecteStatus(Request $request, Collecte $collecte)
    {
        $validator = Validator::make($request->all(), [
            'statut' => 'required|string|in:en_cours,termine,annule,en_pause',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier que la collecte appartient au collecteur
        if ($collecte->collecteur_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à cette collecte.'
            ], 403);
        }

        $updateData = [
            'statut' => $request->statut,
        ];

        // Ajouter les données GPS si fournies
        if ($request->latitude && $request->longitude) {
            $updateData['latitude'] = $request->latitude;
            $updateData['longitude'] = $request->longitude;
            $updateData['accuracy'] = $request->accuracy;
        }

        // Ajouter les notes si fournies
        if ($request->notes) {
            $updateData['notes'] = $request->notes;
        }

        // Si on termine la collecte, ajouter la date de fin
        if ($request->statut === 'termine') {
            $updateData['date_heure_fin'] = now();
        }

        $collecte->update($updateData);

        // Notifier l'administrateur si la collecte est terminée
        if ($request->statut === 'termine') {
            $collecteur = Auth::user();
            $this->notificationService->creerNotification(
                $collecte->itineraire->admin_id,
                'success',
                "Collecte terminée par {$collecteur->name} au point '{$collecte->pointDeCollecte->nom}'.",
                route('admin.collectes.show', $collecte)
            );
        }

        $statutLabels = [
            'en_cours' => 'en cours',
            'termine' => 'terminée',
            'annule' => 'annulée',
            'en_pause' => 'mise en pause'
        ];

        return response()->json([
            'success' => true,
            'message' => "Collecte {$statutLabels[$request->statut]} avec succès"
        ]);
    }

    /**
     * Afficher toutes les collectes du collecteur
     */
    public function indexCollectes(Request $request)
    {
        $collecteur = Auth::user();

        // Redirection vers la tournée en cours (onglet Points) si existante, sauf si ?liste=1
        $itineraireEnCours = $collecteur->itineraires()->where('statut', 'en_cours')->latest()->first();
        if ($itineraireEnCours && !$request->boolean('liste')) {
            return redirect()->route('collecteur.itineraires.show', [$itineraireEnCours->id, 'tab' => 'points'])
                ->with('info', "Une tournée est en cours. Ouverture de l'onglet Points.");
        }

        $query = $collecteur->collectes()->with(['pointDeCollecte', 'itineraire']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        if ($request->filled('type')) {
            $query->where('type_dechet', $request->type);
        }

        $collectes = $query->latest()->paginate(5);

        return view('collecteur.collectes.index', compact('collectes'));
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


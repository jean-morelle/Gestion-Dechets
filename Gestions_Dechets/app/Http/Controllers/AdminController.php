<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Signalement;
use App\Models\Plainte;
use App\Models\Itineraire;
use App\Models\CalendrierCollecte;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tableau de bord administrateur
     */
    public function dashboard()
    {
        $adminId = auth()->id();
        
        $statistiques = [
            'utilisateurs' => [
                'total' => User::count(),
                'citoyens' => User::where('role', 'citoyen')->count(),
                'collecteurs' => User::where('role', 'collecteur')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
            'signalements' => [
                'total' => Signalement::count(),
                'en_attente' => Signalement::where('statut', 'en_attente')->count(),
                'traites' => Signalement::where('statut', 'traite')->count(),
            ],
            'plaintes' => [
                'total' => Plainte::count(),
                'en_attente' => Plainte::where('statut', 'en_attente')->count(),
                'traitees' => Plainte::where('statut', 'traite')->count(),
            ],
            'itineraires' => [
                'total' => Itineraire::count(),
                'actifs' => Itineraire::where('statut', 'en_cours')->count(),
                'termines' => Itineraire::where('statut', 'termine')->count(),
            ],
            'messages' => [
                'total' => Message::where('receiver_id', $adminId)->count(),
                'non_lus' => Message::where('receiver_id', $adminId)->where('is_read', false)->count(),
                'recus' => Message::where('receiver_id', $adminId)->count(),
                'envoyes' => Message::where('sender_id', $adminId)->count(),
            ]
        ];

        $signalementsRecents = Signalement::latest()->limit(5)->get();
        $plaintesRecentes = Plainte::latest()->limit(5)->get();

        return view('admin.dashboard', compact('statistiques', 'signalementsRecents', 'plaintesRecentes'));
    }

    /**
     * Gérer les itinéraires (liste + filtres)
     */
    public function gererItineraires(Request $request)
    {
        $query = Itineraire::with('collecteur')->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('collecteur_id')) {
            $query->where('collecteur_id', $request->collecteur_id);
        }

        $itineraires = $query->paginate(15);

        return view('admin.itineraires.index', compact('itineraires'));
    }

    public function createItineraire()
    {
        $collecteurs = User::where('role', 'collecteur')->orderBy('name')->get(['id','name']);
        return view('admin.itineraires.create', compact('collecteurs'));
    }

    public function storeItineraire(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string',
            'collecteur_id' => 'required|exists:users,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'nullable|string',
        ]);

        Itineraire::create(array_merge(
            $request->only([
                'nom','type','description','collecteur_id','date_debut','date_fin','heure_debut','heure_fin','statut','distance_estimee','duree_estimee','notes'
            ]),
            ['admin_id' => auth()->id()]
        ));

        return redirect()->route('admin.itineraires.index')->with('success', 'Itinéraire créé.');
    }

    public function editItineraire(Itineraire $itineraire)
    {
        return view('admin.itineraires.edit', compact('itineraire'));
    }

    public function updateItineraire(Request $request, Itineraire $itineraire)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string',
        ]);
        $itineraire->update($request->all());
        return redirect()->route('admin.itineraires.index')->with('success', 'Itinéraire mis à jour.');
    }

    public function destroyItineraire(Itineraire $itineraire)
    {
        $itineraire->delete();
        return back()->with('success', 'Itinéraire supprimé.');
    }
    /**
     * Gérer le calendrier de collecte (liste + filtres)
     */
    public function gererCalendrier(Request $request)
    {
        $query = CalendrierCollecte::with('responsable')->latest();

        if ($request->filled('quartier')) {
            $query->where('quartier', $request->quartier);
        }
        if ($request->filled('type_collecte')) {
            $query->where('type_collecte', $request->type_collecte);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $collectes = $query->paginate(5);

        return view('admin.calendrier.index', compact('collectes'));
    }

    public function createCalendrier()
    {
        return view('admin.calendrier.create');
    }

    public function storeCalendrier(Request $request)
    {
        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'heure_debut' => 'required',
            'heure_fin' => 'required|after:heure_debut',
            'type_collecte' => 'required|string',
            'quartier' => 'required|string',
            'frequence' => 'required|string',
            'jour_semaine' => 'required_if:frequence,hebdomadaire|string',
            'statut' => 'required|string|in:actif,inactif,suspendu',
        ]);

        // Générer un nom automatiquement si vide
        $nom = $request->nom;
        if (empty($nom)) {
            $nom = 'Collecte ' . ucfirst($request->type_collecte) . ' - ' . $request->quartier . ' - ' . now()->format('d/m/Y');
        }

        CalendrierCollecte::create([
            'nom' => $nom,
            'type_collecte' => $request->type_collecte,
            'quartier' => $request->quartier,
            'frequence' => $request->frequence,
            'jour_semaine' => $request->jour_semaine,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'description' => $request->description,
            'statut' => $request->statut,
            'responsable_id' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.calendrier.index')->with('success', 'Calendrier de collecte créé avec succès.');
    }

    /**
     * Supervision globale (tableaux et indicateurs)
     */
    public function superviserOperations()
    {
        // Réutiliser des stats du dashboard
        $statistiques = [
            'utilisateurs' => [
                'total' => User::count(),
                'citoyens' => User::where('role', 'citoyen')->count(),
                'collecteurs' => User::where('role', 'collecteur')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
            'signalements' => [
                'total' => Signalement::count(),
                'en_attente' => Signalement::where('statut', 'en_attente')->count(),
                'traite' => Signalement::where('statut', 'traite')->count(),
            ],
            'plaintes' => [
                'total' => Plainte::count(),
                'en_attente' => Plainte::where('statut', 'en_attente')->count(),
                'traite' => Plainte::where('statut', 'traite')->count(),
            ],
            'itineraires' => [
                'total' => Itineraire::count(),
                'en_cours' => Itineraire::where('statut', 'en_cours')->count(),
                'termine' => Itineraire::where('statut', 'termine')->count(),
            ],
        ];

        return view('admin.supervision.index', compact('statistiques'));
    }
    /**
     * Gérer les signalements
     */
    public function gererSignalements(Request $request)
    {
        $query = Signalement::with('user')->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_dechet')) {
            $query->where('type_dechet', $request->type_dechet);
        }

        if ($request->filled('quartier')) {
            $query->where('quartier', $request->quartier);
        }

        $signalements = $query->paginate(5);

        return view('admin.signalements.index', compact('signalements'));
    }

    /**
     * Afficher un signalement
     */
    public function afficherSignalement(Signalement $signalement)
    {
        return view('admin.signalements.show', compact('signalement'));
    }

    /**
     * Mettre à jour un signalement
     */
    public function updateSignalement(Request $request, Signalement $signalement)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,traite,rejete',
            'priorite' => 'required|in:faible,moyenne,elevee,urgente',
            'commentaires_admin' => 'nullable|string|max:1000',
        ]);

        $signalement->update($request->only(['statut', 'priorite', 'commentaires_admin']));

        return redirect()->route('admin.signalements.show', $signalement)
            ->with('success', 'Signalement mis à jour avec succès.');
    }

    /**
     * Supprimer un signalement
     */
    public function destroySignalement(Signalement $signalement)
    {
        $signalement->delete();

        return redirect()->route('admin.signalements.index')
            ->with('success', 'Signalement supprimé avec succès.');
    }

    /**
     * Traiter un signalement
     */
    public function traiterSignalement(Request $request, Signalement $signalement)
    {
        $request->validate([
            'statut' => 'required|in:en_cours,traite,annule',
            'priorite' => 'required|in:faible,moyenne,elevee,urgente',
            'notes' => 'nullable|string|max:1000',
            'date_collecte_prevue' => 'nullable|date|after:today',
        ]);

        $signalement->update($request->only(['statut', 'priorite', 'notes', 'date_collecte_prevue']));

        // Notifier le citoyen
        $this->notificationService->creerNotification(
            $signalement->user_id,
            'info',
            "Votre signalement a été mis à jour : {$signalement->statut_label}",
            route('citoyen.signalements.show', $signalement)
        );

        return redirect()->route('admin.signalements.show', $signalement)
                        ->with('success', 'Signalement traité avec succès !');
    }

    /**
     * Gérer les utilisateurs
     */
    public function gererComptes(Request $request)
    {
        $query = User::latest();

        // Filtres
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('quartier', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(5);

        return view('admin.utilisateurs.index', compact('users'));
    }

    /**
     * Afficher un utilisateur
     */
    public function afficherUtilisateur(User $user)
    {
        return view('admin.utilisateurs.show', compact('user'));
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroyUtilisateur(User $user)
    {
        $user->delete();

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Modifier le statut d'un utilisateur
     */
    public function modifierStatutUtilisateur(Request $request, User $user)
    {
        $request->validate([
            'statut' => 'required|in:actif,inactif,suspendu',
            'role' => 'nullable|in:citoyen,collecteur,admin',
        ]);

        $updateData = ['statut' => $request->statut];
        if ($request->filled('role')) {
            $updateData['role'] = $request->role;
        }

        $user->update($updateData);

        return redirect()->route('admin.utilisateurs.show', $user)
                        ->with('success', 'Statut utilisateur modifié avec succès !');
    }

    /**
     * Gérer les plaintes
     */
    public function gererPlaintes(Request $request)
    {
        $query = Plainte::with('user')->latest();

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_plainte')) {
            $query->where('type_plainte', $request->type_plainte);
        }

        $plaintes = $query->paginate(5);

        return view('admin.plaintes.index', compact('plaintes'));
    }

    /**
     * Afficher une plainte
     */
    public function afficherPlainte(Plainte $plainte)
    {
        return view('admin.plaintes.show', compact('plainte'));
    }

    /**
     * Supprimer une plainte
     */
    public function destroyPlainte(Plainte $plainte)
    {
        $plainte->delete();

        return redirect()->route('admin.plaintes.index')
            ->with('success', 'Plainte supprimée avec succès.');
    }

    /**
     * Traiter une plainte
     */
    public function traiterPlainte(Request $request, Plainte $plainte)
    {
        $request->validate([
            'statut' => 'required|in:en_cours,traite,ferme',
            'reponse' => 'required|string|max:2000',
        ]);

        $plainte->update([
            'statut' => $request->statut,
            'reponse_admin' => $request->reponse,
            'date_traitement' => now(),
        ]);

        // Notifier le citoyen
        $this->notificationService->creerNotification(
            $plainte->user_id,
            'info',
            "Votre plainte a été traitée : {$plainte->statut_label}",
            route('citoyen.plaintes.show', $plainte)
        );

        return redirect()->route('admin.plaintes.show', $plainte)
                        ->with('success', 'Plainte traitée avec succès !');
    }

    /**
     * Fermer une plainte
     */
    public function fermerPlainte(Request $request, Plainte $plainte)
    {
        $request->validate([
            'reponse_finale' => 'required|string|max:2000',
        ]);

        $plainte->update([
            'statut' => 'ferme',
            'reponse_admin' => $request->reponse_finale,
            'date_traitement' => now(),
        ]);

        // Notifier le citoyen
        $this->notificationService->creerNotification(
            $plainte->user_id,
            'info',
            "Votre plainte a été fermée : " . $request->reponse_finale
        );

        return redirect()->route('admin.plaintes.index')
            ->with('success', 'Plainte fermée avec succès.');
    }
}


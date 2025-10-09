<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * Afficher la liste des notifications envoyées
     */
    public function index()
    {
        $notifications = Notification::with(['user', 'expediteur'])
            ->where('expediteur_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $statistiques = [
            'total' => $notifications->total(),
            'non_lues' => Notification::where('expediteur_id', Auth::id())
                ->where('statut', Notification::STATUT_NON_LU)
                ->count(),
            'lues' => Notification::where('expediteur_id', Auth::id())
                ->where('statut', Notification::STATUT_LU)
                ->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'statistiques'));
    }

    /**
     * Afficher le formulaire de création de notification
     */
    public function create()
    {
        // Récupérer tous les utilisateurs sauf l'admin connecté
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('role')
            ->orderBy('name')
            ->get()
            ->groupBy('role');

        return view('admin.notifications.create', compact('users'));
    }

    /**
     * Envoyer une notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'destinataires' => 'required|array|min:1',
            'destinataires.*' => 'exists:users,id',
            'type' => 'required|in:signalement,plainte,paiement,collecte,itineraire,calendrier,systeme',
            'titre' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'priorite' => 'required|in:faible,moyenne,elevee,urgente',
            'lien_action' => 'nullable|url',
            'icone' => 'nullable|string|max:50',
        ]);

        $notificationsCreees = 0;

        foreach ($request->destinataires as $userId) {
            Notification::create([
                'user_id' => $userId,
                'expediteur_id' => Auth::id(),
                'type' => $request->type,
                'titre' => $request->titre,
                'message' => $request->message,
                'priorite' => $request->priorite,
                'lien_action' => $request->lien_action,
                'icone' => $request->icone,
                'date_envoi' => now(),
                'statut' => Notification::STATUT_NON_LU,
            ]);
            $notificationsCreees++;
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification envoyée à {$notificationsCreees} destinataire(s) !");
    }

    /**
     * Afficher les détails d'une notification
     */
    public function show(Notification $notification)
    {
        // Vérifier que l'admin est l'expéditeur
        if ($notification->expediteur_id !== Auth::id()) {
            abort(403);
        }

        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Notification $notification)
    {
        // Vérifier que l'admin est l'expéditeur
        if ($notification->expediteur_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification supprimée avec succès !');
    }

    /**
     * Envoyer une notification d'urgence (tous les utilisateurs)
     */
    public function createUrgence()
    {
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('role')
            ->orderBy('name')
            ->get()
            ->groupBy('role');

        return view('admin.notifications.urgence', compact('users'));
    }

    /**
     * Envoyer la notification d'urgence
     */
    public function storeUrgence(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'lien_action' => 'nullable|url',
        ]);

        // Envoyer à tous les utilisateurs sauf l'admin
        $users = User::where('id', '!=', Auth::id())->get();
        $notificationsCreees = 0;

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'expediteur_id' => Auth::id(),
                'type' => Notification::TYPE_SYSTEME,
                'titre' => $request->titre,
                'message' => $request->message,
                'priorite' => Notification::PRIORITE_URGENTE,
                'lien_action' => $request->lien_action,
                'icone' => 'fas fa-exclamation-triangle',
                'date_envoi' => now(),
                'statut' => Notification::STATUT_NON_LU,
            ]);
            $notificationsCreees++;
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification d'urgence envoyée à {$notificationsCreees} utilisateur(s) !");
    }

    /**
     * Notifier l'attribution d'une nouvelle mission
     */
    public function notifierNouvelleMission($collecteurId, $missionData)
    {
        $collecteur = User::findOrFail($collecteurId);
        
        Notification::create([
            'user_id' => $collecteurId,
            'expediteur_id' => Auth::id(),
            'type' => Notification::TYPE_COLLECTE,
            'titre' => 'Nouvelle mission assignée',
            'message' => "Une nouvelle tournée de collecte vous a été assignée : {$missionData['nom']}. Date prévue : {$missionData['date']}",
            'priorite' => Notification::PRIORITE_ELEVEE,
            'lien_action' => route('collecteur.itineraires.index'),
            'icone' => 'fas fa-route',
            'date_envoi' => now(),
            'statut' => Notification::STATUT_NON_LU,
            'data' => $missionData,
        ]);
    }

    /**
     * Notifier la modification d'un itinéraire
     */
    public function notifierModificationItineraire($collecteurId, $itineraireData)
    {
        $collecteur = User::findOrFail($collecteurId);
        
        Notification::create([
            'user_id' => $collecteurId,
            'expediteur_id' => Auth::id(),
            'type' => Notification::TYPE_ITINERAIRE,
            'titre' => 'Itinéraire modifié',
            'message' => "Votre itinéraire '{$itineraireData['nom']}' a été modifié. Veuillez consulter les nouveaux détails.",
            'priorite' => Notification::PRIORITE_MOYENNE,
            'lien_action' => route('collecteur.itineraires.show', $itineraireData['id']),
            'icone' => 'fas fa-route',
            'date_envoi' => now(),
            'statut' => Notification::STATUT_NON_LU,
            'data' => $itineraireData,
        ]);
    }

    /**
     * Notifier un rappel ou message d'urgence
     */
    public function notifierRappelUrgence($userIds, $titre, $message, $priorite = Notification::PRIORITE_ELEVEE)
    {
        $notificationsCreees = 0;

        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'expediteur_id' => Auth::id(),
                'type' => Notification::TYPE_SYSTEME,
                'titre' => $titre,
                'message' => $message,
                'priorite' => $priorite,
                'icone' => 'fas fa-exclamation-triangle',
                'date_envoi' => now(),
                'statut' => Notification::STATUT_NON_LU,
            ]);
            $notificationsCreees++;
        }

        return $notificationsCreees;
    }
}
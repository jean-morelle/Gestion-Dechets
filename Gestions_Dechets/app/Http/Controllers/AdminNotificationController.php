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
    public function index(Request $request)
    {
        $onglet = $request->query('onglet', 'recues');

        // Reçues : alertes de l'application (nouveau signalement, incident, tournée terminée…)
        // Envoyées : messages que l'administrateur a adressés aux habitants ou aux agents
        $notifications = Notification::with(['user', 'expediteur'])
            ->where($onglet === 'envoyees' ? 'expediteur_id' : 'user_id', Auth::id())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $nonLues = Notification::where('user_id', Auth::id())->where('statut', Notification::STATUT_NON_LU)->count();

        return view('admin.notifications.index', compact('notifications', 'onglet', 'nonLues'));
    }

    /**
     * Ouvrir une notification reçue : elle est marquée lue et l'on suit son lien
     */
    public function ouvrir(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->marquerCommeLue();

        return $notification->lien_action
            ? redirect()->to($notification->lien_action)
            : redirect()->route('admin.notifications.index');
    }

    public function toutMarquerLu()
    {
        Notification::where('user_id', Auth::id())
            ->where('statut', Notification::STATUT_NON_LU)
            ->update(['statut' => Notification::STATUT_LU, 'date_lecture' => now()]);

        return back()->with('success', 'Toutes les notifications sont marquées comme lues.');
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
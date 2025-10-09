<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Créer une nouvelle notification
     */
    public function creerNotification($userId, $type, $titre, $message, $lien = null)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'titre' => $titre,
            'message' => $message,
            'lien_action' => $lien,
            'statut' => Notification::STATUT_NON_LU,
            'priorite' => Notification::PRIORITE_MOYENNE,
            'date_envoi' => now(),
        ]);

        return $notification;
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerCommeLue($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->update([
            'statut' => Notification::STATUT_LU,
            'date_lecture' => now()
        ]);
        return $notification;
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function marquerToutesCommeLues($userId)
    {
        Notification::where('user_id', $userId)
                   ->where('statut', Notification::STATUT_NON_LU)
                   ->update([
                       'statut' => Notification::STATUT_LU,
                       'date_lecture' => now()
                   ]);
    }

    /**
     * Obtenir les notifications non lues d'un utilisateur
     */
    public function obtenirNotificationsNonLues($userId)
    {
        return Notification::where('user_id', $userId)
                          ->where('statut', Notification::STATUT_NON_LU)
                          ->orderBy('date_envoi', 'desc')
                          ->get();
    }

    /**
     * Obtenir toutes les notifications d'un utilisateur
     */
    public function obtenirNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
                          ->orderBy('date_envoi', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Compter les notifications non lues
     */
    public function compterNotificationsNonLues($userId)
    {
        return Notification::where('user_id', $userId)
                          ->where('statut', Notification::STATUT_NON_LU)
                          ->count();
    }

    /**
     * Supprimer une notification
     */
    public function supprimerNotification($notificationId)
    {
        return Notification::destroy($notificationId);
    }

    /**
     * Supprimer toutes les notifications d'un utilisateur
     */
    public function supprimerToutesNotifications($userId)
    {
        return Notification::where('user_id', $userId)->delete();
    }

    /**
     * Notifier un nouveau signalement
     */
    public function notifierNouveauSignalement($signalement)
    {
        // Notifier l'administrateur
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->creerNotification(
                $admin->id,
                'signalement',
                'Nouveau signalement',
                "Nouveau signalement de " . $signalement->user->name . " : " . $signalement->type_dechet_label,
                route('citoyen.signalements.show', $signalement->id)
            );
        }
    }
}
















































































































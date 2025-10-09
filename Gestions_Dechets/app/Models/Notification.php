<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Constantes pour les types de notifications
    const TYPE_SIGNALEMENT = 'signalement';
    const TYPE_PLAINTE = 'plainte';
    const TYPE_PAIEMENT = 'paiement';
    const TYPE_COLLECTE = 'collecte';
    const TYPE_ITINERAIRE = 'itineraire';
    const TYPE_CALENDRIER = 'calendrier';
    const TYPE_SYSTEME = 'systeme';

    // Constantes pour les statuts
    const STATUT_NON_LU = 'non_lu';
    const STATUT_LU = 'lu';
    const STATUT_ARCHIVE = 'archive';

    // Constantes pour les niveaux de priorité
    const PRIORITE_FAIBLE = 'faible';
    const PRIORITE_MOYENNE = 'moyenne';
    const PRIORITE_ELEVEE = 'elevee';
    const PRIORITE_URGENTE = 'urgente';

    protected $fillable = [
        'user_id',
        'type',
        'titre',
        'message',
        'data',
        'statut',
        'priorite',
        'date_envoi',
        'date_lecture',
        'expediteur_id',
        'lien_action',
        'icone',
    ];

    protected $casts = [
        'data' => 'array',
        'date_envoi' => 'datetime',
        'date_lecture' => 'datetime',
    ];

    /**
     * Relations avec les autres modèles
     */

    // Une notification appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Une notification peut avoir un expéditeur
    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    /**
     * Méthodes utilitaires
     */

    // Vérifier si la notification est non lue
    public function isNonLue()
    {
        return $this->statut === self::STATUT_NON_LU;
    }

    // Vérifier si la notification est lue
    public function isLue()
    {
        return $this->statut === self::STATUT_LU;
    }

    // Marquer comme lue
    public function marquerCommeLue()
    {
        $this->update([
            'statut' => self::STATUT_LU,
            'date_lecture' => now(),
        ]);
    }

    // Marquer comme archivée
    public function archiver()
    {
        $this->update(['statut' => self::STATUT_ARCHIVE]);
    }

    // Obtenir le libellé du type
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_SIGNALEMENT => 'Signalement',
            self::TYPE_PLAINTE => 'Plainte',
            self::TYPE_PAIEMENT => 'Paiement',
            self::TYPE_COLLECTE => 'Collecte',
            self::TYPE_ITINERAIRE => 'Itinéraire',
            self::TYPE_CALENDRIER => 'Calendrier',
            self::TYPE_SYSTEME => 'Système',
            default => 'Non défini'
        };
    }

    // Obtenir le libellé du statut
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            self::STATUT_NON_LU => 'Non lu',
            self::STATUT_LU => 'Lu',
            self::STATUT_ARCHIVE => 'Archivé',
            default => 'Non défini'
        };
    }

    // Obtenir le libellé de la priorité
    public function getPrioriteLabelAttribute()
    {
        return match($this->priorite) {
            self::PRIORITE_FAIBLE => 'Faible',
            self::PRIORITE_MOYENNE => 'Moyenne',
            self::PRIORITE_ELEVEE => 'Élevée',
            self::PRIORITE_URGENTE => 'Urgente',
            default => 'Non définie'
        };
    }

    // Obtenir la classe CSS pour le statut
    public function getStatutClassAttribute()
    {
        return match($this->statut) {
            self::STATUT_NON_LU => 'badge bg-danger',
            self::STATUT_LU => 'badge bg-success',
            self::STATUT_ARCHIVE => 'badge bg-secondary',
            default => 'badge bg-light'
        };
    }

    // Obtenir la classe CSS pour la priorité
    public function getPrioriteClassAttribute()
    {
        return match($this->priorite) {
            self::PRIORITE_FAIBLE => 'text-secondary',
            self::PRIORITE_MOYENNE => 'text-warning',
            self::PRIORITE_ELEVEE => 'text-danger',
            self::PRIORITE_URGENTE => 'text-dark fw-bold',
            default => 'text-muted'
        };
    }

    // Obtenir l'icône par défaut selon le type
    public function getIconeParDefautAttribute()
    {
        return match($this->type) {
            self::TYPE_SIGNALEMENT => 'fas fa-exclamation-triangle',
            self::TYPE_PLAINTE => 'fas fa-comment-dots',
            self::TYPE_PAIEMENT => 'fas fa-credit-card',
            self::TYPE_COLLECTE => 'fas fa-recycle',
            self::TYPE_ITINERAIRE => 'fas fa-route',
            self::TYPE_CALENDRIER => 'fas fa-calendar-alt',
            self::TYPE_SYSTEME => 'fas fa-cog',
            default => 'fas fa-bell'
        };
    }

    // Obtenir l'icône à afficher
    public function getIconeAAfficherAttribute()
    {
        return $this->icone ?: $this->icone_par_defaut;
    }

    // Calculer le temps écoulé depuis l'envoi
    public function getTempsEcouleAttribute()
    {
        return $this->date_envoi->diffForHumans();
    }

    /**
     * Méthodes statiques pour créer des notifications
     */

    // Créer une notification de signalement
    public static function creerSignalement($userId, $signalementId, $message)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_SIGNALEMENT,
            'titre' => 'Nouveau signalement',
            'message' => $message,
            'data' => ['signalement_id' => $signalementId],
            'statut' => self::STATUT_NON_LU,
            'priorite' => self::PRIORITE_MOYENNE,
            'date_envoi' => now(),
            'lien_action' => route('signalements.show', $signalementId),
        ]);
    }

    // Créer une notification de plainte
    public static function creerPlainte($userId, $plainteId, $message)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_PLAINTE,
            'titre' => 'Nouvelle plainte',
            'message' => $message,
            'data' => ['plainte_id' => $plainteId],
            'statut' => self::STATUT_NON_LU,
            'priorite' => self::PRIORITE_ELEVEE,
            'date_envoi' => now(),
            'lien_action' => route('plaintes.show', $plainteId),
        ]);
    }

    // Créer une notification de paiement
    public static function creerPaiement($userId, $paiementId, $message)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_PAIEMENT,
            'titre' => 'Paiement',
            'message' => $message,
            'data' => ['paiement_id' => $paiementId],
            'statut' => self::STATUT_NON_LU,
            'priorite' => self::PRIORITE_MOYENNE,
            'date_envoi' => now(),
            'lien_action' => route('paiements.show', $paiementId),
        ]);
    }

    // Créer une notification de collecte
    public static function creerCollecte($userId, $collecteId, $message)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_COLLECTE,
            'titre' => 'Collecte',
            'message' => $message,
            'data' => ['collecte_id' => $collecteId],
            'statut' => self::STATUT_NON_LU,
            'priorite' => self::PRIORITE_MOYENNE,
            'date_envoi' => now(),
            'lien_action' => route('collectes.show', $collecteId),
        ]);
    }

    // Créer une notification système
    public static function creerSysteme($userId, $titre, $message, $priorite = self::PRIORITE_MOYENNE)
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_SYSTEME,
            'titre' => $titre,
            'message' => $message,
            'statut' => self::STATUT_NON_LU,
            'priorite' => $priorite,
            'date_envoi' => now(),
        ]);
    }

    /**
     * Méthodes de requête
     */

    // Notifications non lues pour un utilisateur
    public function scopeNonLues($query, $userId)
    {
        return $query->where('user_id', $userId)->where('statut', self::STATUT_NON_LU);
    }

    // Notifications par type
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Notifications par priorité
    public function scopeParPriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    // Notifications récentes
    public function scopeRecent($query, $jours = 7)
    {
        return $query->where('date_envoi', '>=', now()->subDays($jours));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    use HasFactory;

    // Constantes pour les types de déchets
    const TYPE_DECHET_MENAGER = 'dechet_menager';
    const TYPE_DECHET_VERT = 'dechet_vert';
    const TYPE_DECHET_ENCOMBRANT = 'encombrant';
    const TYPE_DECHET_DANGEREUX = 'dechet_dangereux';
    const TYPE_DECHET_RECYCLABLE = 'dechet_recyclable';
    const TYPE_DECHET_AUTRE = 'autre';

    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TRAITE = 'traite';
    const STATUT_ANNULE = 'annule';

    // Constantes pour les niveaux de priorité
    const PRIORITE_FAIBLE = 'faible';
    const PRIORITE_MOYENNE = 'moyenne';
    const PRIORITE_ELEVEE = 'elevee';
    const PRIORITE_URGENTE = 'urgente';

    protected $fillable = [
        'user_id',
        'type_dechet',
        'description',
        'adresse',
        'quartier',
        'latitude',
        'longitude',
        'photo',
        'statut',
        'priorite',
        'urgence',
        'contact_telephone',
        'date_collecte_prevue',
        'date_collecte_reelle',
        'collecteur_id',
        'notes_admin',
        'commentaires_admin',
    ];

    protected $casts = [
        'date_collecte_prevue' => 'datetime',
        'date_collecte_reelle' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relations avec les autres modèles
     */

    // Un signalement appartient à un utilisateur (citoyen)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un signalement peut être assigné à un collecteur
    public function collecteur()
    {
        return $this->belongsTo(User::class, 'collecteur_id');
    }

    // Un signalement peut avoir plusieurs collectes
    public function collectes()
    {
        return $this->hasMany(Collecte::class);
    }

    // Un signalement peut générer des notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Méthodes utilitaires
     */

    // Vérifier si le signalement est en attente
    public function isEnAttente()
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    // Vérifier si le signalement est traité
    public function isTraite()
    {
        return $this->statut === self::STATUT_TRAITE;
    }

    // Obtenir le libellé du type de déchet
    public function getTypeDechetLabelAttribute()
    {
        return match($this->type_dechet) {
            self::TYPE_DECHET_MENAGER => 'Déchet ménager',
            self::TYPE_DECHET_VERT => 'Déchet vert',
            self::TYPE_DECHET_ENCOMBRANT => 'Encombrant',
            self::TYPE_DECHET_DANGEREUX => 'Déchet dangereux',
            self::TYPE_DECHET_RECYCLABLE => 'Déchet recyclable',
            self::TYPE_DECHET_AUTRE => 'Autre',
            default => 'Non défini'
        };
    }

    // Obtenir le libellé du statut
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TRAITE => 'Traité',
            self::STATUT_ANNULE => 'Annulé',
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

    // Obtenir la classe CSS pour la priorité
    public function getPrioriteClassAttribute()
    {
        return match($this->priorite) {
            self::PRIORITE_FAIBLE => 'badge bg-secondary',
            self::PRIORITE_MOYENNE => 'badge bg-warning',
            self::PRIORITE_ELEVEE => 'badge bg-danger',
            self::PRIORITE_URGENTE => 'badge bg-dark',
            default => 'badge bg-light'
        };
    }

    // Obtenir la classe CSS pour le statut
    public function getStatutClassAttribute()
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'badge bg-warning',
            self::STATUT_EN_COURS => 'badge bg-info',
            self::STATUT_TRAITE => 'badge bg-success',
            self::STATUT_ANNULE => 'badge bg-danger',
            default => 'badge bg-light'
        };
    }

    // Obtenir le libellé de l'urgence
    public function getUrgenceLabelAttribute()
    {
        return match($this->urgence) {
            'faible' => 'Faible',
            'moyenne' => 'Moyenne',
            'elevee' => 'Élevée',
            'urgente' => 'Urgente',
            default => 'Normale'
        };
    }

    // Obtenir la classe CSS pour l'urgence
    public function getUrgenceClassAttribute()
    {
        return match($this->urgence) {
            'faible' => 'badge bg-secondary',
            'moyenne' => 'badge bg-info',
            'elevee' => 'badge bg-warning',
            'urgente' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }
}

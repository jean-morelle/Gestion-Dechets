<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plainte extends Model
{
    use HasFactory;

    // Constantes pour les types de plaintes
    const TYPE_COLLECTE_RETARD = 'collecte_retard';
    const TYPE_COLLECTE_OUBLIEE = 'collecte_oubliee';
    const TYPE_SERVICE_CLIENT = 'service_client';
    const TYPE_AUTRE = 'autre';

    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TRAITE = 'traite';
    const STATUT_FERME = 'ferme';

    // Constantes pour les niveaux de priorité
    const PRIORITE_FAIBLE = 'faible';
    const PRIORITE_MOYENNE = 'moyenne';
    const PRIORITE_ELEVEE = 'elevee';
    const PRIORITE_URGENTE = 'urgente';

    protected $fillable = [
        'user_id',
        'type_plainte',
        'sujet',
        'description',
        'adresse',
        'quartier',
        'latitude',
        'longitude',
        'photo',
        'contact_telephone',
        'signalement_id',
        'statut',
        'priorite',
        'date_traitement',
        'reponse',
        'traite_par',
    ];

    protected $casts = [
        'date_traitement' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relations avec les autres modèles
     */

    // Une plainte appartient à un utilisateur (citoyen)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Une plainte peut être liée à un signalement
    public function signalement()
    {
        return $this->belongsTo(Signalement::class);
    }

    // Une plainte peut être traitée par un admin
    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    // Une plainte peut générer des notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Méthodes utilitaires
     */

    // Vérifier si la plainte est en attente
    public function isEnAttente()
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    // Vérifier si la plainte est traitée
    public function isTraite()
    {
        return $this->statut === self::STATUT_TRAITE;
    }

    // Vérifier si la plainte est fermée
    public function isFerme()
    {
        return $this->statut === self::STATUT_FERME;
    }

    // Obtenir le libellé du type de plainte
    public function getTypePlainteLabelAttribute()
    {
        return match($this->type_plainte) {
            self::TYPE_COLLECTE_RETARD => 'Collecte en retard',
            self::TYPE_COLLECTE_OUBLIEE => 'Collecte oubliée',
            self::TYPE_SERVICE_CLIENT => 'Service client',
            self::TYPE_AUTRE => 'Autre',
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
            self::STATUT_FERME => 'Fermé',
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
            self::PRIORITE_URGENTE => 'badge bg-danger',
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
            self::STATUT_FERME => 'badge bg-secondary',
            default => 'badge bg-light'
        };
    }

}

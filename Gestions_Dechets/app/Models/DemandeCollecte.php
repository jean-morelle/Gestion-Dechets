<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCollecte extends Model
{
    use HasFactory;

    // Constantes pour les types de collecte
    const TYPE_MENAGERE = 'menagere';
    const TYPE_ENCOMBRANT = 'encombrant';
    const TYPE_VERT = 'vert';
    const TYPE_RECYCLABLE = 'recyclable';
    const TYPE_DANGEREUX = 'dangereux';
    const TYPE_DEMENAGEMENT = 'demenagement';

    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_ACCEPTE = 'accepte';
    const STATUT_REFUSE = 'refuse';
    const STATUT_TERMINE = 'termine';

    // Constantes pour les niveaux d'urgence
    const URGENCE_FAIBLE = 'faible';
    const URGENCE_MOYENNE = 'moyenne';
    const URGENCE_ELEVEE = 'elevee';
    const URGENCE_URGENTE = 'urgente';

    protected $fillable = [
        'user_id',
        'type_collecte',
        'objet',
        'description',
        'adresse',
        'quartier',
        'latitude',
        'longitude',
        'urgence',
        'date_souhaitee',
        'heure_souhaitee',
        'contact_telephone',
        'instructions_speciales',
        'photo',
        'montant_estime',
        'statut',
        'raison_refus',
        'date_traitement',
        'admin_id',
        'collecteur_id',
        'date_collecte_prevue',
    ];

    protected $casts = [
        'date_souhaitee' => 'date',
        'heure_souhaitee' => 'datetime:H:i',
        'date_traitement' => 'datetime',
        'date_collecte_prevue' => 'datetime',
        'montant_estime' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relations avec les autres modèles
     */

    // Une demande de collecte appartient à un utilisateur (citoyen)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Une demande de collecte peut être traitée par un admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Une demande de collecte peut être assignée à un collecteur
    public function collecteur()
    {
        return $this->belongsTo(User::class, 'collecteur_id');
    }

    // Une demande de collecte peut générer des notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Méthodes utilitaires
     */

    // Vérifier si la demande est en attente
    public function isEnAttente()
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    // Vérifier si la demande est acceptée
    public function isAccepte()
    {
        return $this->statut === self::STATUT_ACCEPTE;
    }

    // Vérifier si la demande est refusée
    public function isRefuse()
    {
        return $this->statut === self::STATUT_REFUSE;
    }

    // Vérifier si la demande est terminée
    public function isTermine()
    {
        return $this->statut === self::STATUT_TERMINE;
    }

    // Obtenir le libellé du type de collecte
    public function getTypeCollecteLabelAttribute()
    {
        return match($this->type_collecte) {
            self::TYPE_MENAGERE => 'Ménagère',
            self::TYPE_ENCOMBRANT => 'Encombrant',
            self::TYPE_VERT => 'Vert',
            self::TYPE_RECYCLABLE => 'Recyclable',
            self::TYPE_DANGEREUX => 'Dangereux',
            self::TYPE_DEMENAGEMENT => 'Déménagement',
            default => 'Non défini'
        };
    }

    // Obtenir le libellé du statut
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_ACCEPTE => 'Accepté',
            self::STATUT_REFUSE => 'Refusé',
            self::STATUT_TERMINE => 'Terminé',
            default => 'Non défini'
        };
    }

    // Obtenir le libellé de l'urgence
    public function getUrgenceLabelAttribute()
    {
        return match($this->urgence) {
            self::URGENCE_FAIBLE => 'Faible',
            self::URGENCE_MOYENNE => 'Moyenne',
            self::URGENCE_ELEVEE => 'Élevée',
            self::URGENCE_URGENTE => 'Urgente',
            default => 'Non définie'
        };
    }

    // Obtenir la classe CSS pour le statut
    public function getStatutClassAttribute()
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'badge bg-warning',
            self::STATUT_EN_COURS => 'badge bg-info',
            self::STATUT_ACCEPTE => 'badge bg-success',
            self::STATUT_REFUSE => 'badge bg-danger',
            self::STATUT_TERMINE => 'badge bg-secondary',
            default => 'badge bg-light'
        };
    }

    /** Couleur de badge (classes tone-* de app.css) */
    public function getStatutToneAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'tone-amber',
            self::STATUT_ACCEPTE, self::STATUT_EN_COURS => 'tone-blue',
            self::STATUT_TERMINE => 'tone-green',
            self::STATUT_REFUSE => 'tone-red',
            default => 'tone-slate',
        };
    }

    public function getUrgenceToneAttribute(): string
    {
        return match ($this->urgence) {
            self::URGENCE_URGENTE => 'tone-red',
            self::URGENCE_ELEVEE => 'tone-amber',
            default => 'tone-slate',
        };
    }

    // Obtenir la classe CSS pour l'urgence
    public function getUrgenceClassAttribute()
    {
        return match($this->urgence) {
            self::URGENCE_FAIBLE => 'badge bg-secondary',
            self::URGENCE_MOYENNE => 'badge bg-info',
            self::URGENCE_ELEVEE => 'badge bg-warning',
            self::URGENCE_URGENTE => 'badge bg-danger',
            default => 'badge bg-light'
        };
    }

    // Obtenir la classe CSS pour le type de collecte
    public function getTypeCollecteClassAttribute()
    {
        return match($this->type_collecte) {
            self::TYPE_MENAGERE => 'badge bg-primary',
            self::TYPE_ENCOMBRANT => 'badge bg-warning',
            self::TYPE_VERT => 'badge bg-success',
            self::TYPE_RECYCLABLE => 'badge bg-info',
            self::TYPE_DANGEREUX => 'badge bg-danger',
            self::TYPE_DEMENAGEMENT => 'badge bg-secondary',
            default => 'badge bg-light'
        };
    }

    // Scope pour les demandes en attente
    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    // Scope pour les demandes acceptées
    public function scopeAcceptees($query)
    {
        return $query->where('statut', self::STATUT_ACCEPTE);
    }

    // Scope pour un type de collecte spécifique
    public function scopeTypeCollecte($query, $type)
    {
        return $query->where('type_collecte', $type);
    }

    // Scope pour un niveau d'urgence spécifique
    public function scopeUrgence($query, $urgence)
    {
        return $query->where('urgence', $urgence);
    }

    // Scope pour un quartier spécifique
    public function scopeQuartier($query, $quartier)
    {
        return $query->where('quartier', $quartier);
    }

    // Vérifier si la demande est urgente
    public function isUrgente()
    {
        return in_array($this->urgence, [self::URGENCE_ELEVEE, self::URGENCE_URGENTE]);
    }

    // Obtenir le temps écoulé depuis la création
    public function getTempsEcouleAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Obtenir la date de collecte formatée
    public function getDateCollecteFormateeAttribute()
    {
        if ($this->date_collecte_prevue) {
            return $this->date_collecte_prevue->format('d/m/Y H:i');
        }
        
        if ($this->date_souhaitee) {
            return $this->date_souhaitee->format('d/m/Y');
        }
        
        return 'Non définie';
    }
}
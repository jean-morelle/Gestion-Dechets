<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itineraire extends Model
{
    use HasFactory;

    // Constantes pour les types d'itinéraires
    const TYPE_QUOTIDIEN = 'quotidien';
    const TYPE_HEBDOMADAIRE = 'hebdomadaire';
    const TYPE_MENSUEL = 'mensuel';
    const TYPE_PONCTUEL = 'ponctuel';

    // Constantes pour les statuts
    const STATUT_PLANIFIE = 'planifie';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_ANNULE = 'annule';

    protected $fillable = [
        'nom',
        'type',
        'description',
        'collecteur_id',
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
        'statut',
        'distance_estimee',
        'duree_estimee',
        'notes',
        'admin_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'distance_estimee' => 'decimal:2',
        'duree_estimee' => 'integer'
    ];

    // Relations
    public function collecteur()
    {
        return $this->belongsTo(User::class, 'collecteur_id');
    }

    public function collectes()
    {
        return $this->hasMany(Collecte::class);
    }

    public function pointsDeCollecte()
    {
        return $this->belongsToMany(PointDeCollecte::class, 'itineraire_points')
                    ->withPivot('ordre')
                    ->orderBy('itineraire_points.ordre');
    }

    public function itinerairePoints()
    {
        return $this->hasMany(ItinerairePoint::class);
    }

    // Accesseurs pour les libellés
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_QUOTIDIEN => 'Quotidien',
            self::TYPE_HEBDOMADAIRE => 'Hebdomadaire',
            self::TYPE_MENSUEL => 'Mensuel',
            self::TYPE_PONCTUEL => 'Ponctuel',
            default => 'Non défini'
        };
    }

    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            self::STATUT_PLANIFIE => 'Planifié',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Terminé',
            self::STATUT_ANNULE => 'Annulé',
            default => 'Non défini'
        };
    }

    public function getStatutClassAttribute()
    {
        return match($this->statut) {
            self::STATUT_PLANIFIE => 'badge bg-info',
            self::STATUT_EN_COURS => 'badge bg-primary',
            self::STATUT_TERMINE => 'badge bg-success',
            self::STATUT_ANNULE => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    // Méthodes utilitaires
    public function calculerDistance()
    {
        // Logique pour calculer la distance de l'itinéraire
        return $this->distance_estimee;
    }

    public function calculerDuree()
    {
        // Logique pour calculer la durée de l'itinéraire
        return $this->duree_estimee;
    }

    public function estActif()
    {
        return $this->statut === self::STATUT_PLANIFIE || $this->statut === self::STATUT_EN_COURS;
    }

    public function peutEtreModifie()
    {
        return $this->statut === self::STATUT_PLANIFIE;
    }

    public function annuler()
    {
        $this->update(['statut' => self::STATUT_ANNULE]);
        
        // Annuler toutes les collectes associées
        $this->collectes()->where('statut', 'en_attente')->update(['statut' => 'annule']);
    }

    public function terminer()
    {
        $this->update(['statut' => self::STATUT_TERMINE]);
        
        // Terminer toutes les collectes en cours
        $this->collectes()->where('statut', 'en_cours')->update(['statut' => 'termine']);
    }

    public function demarrer()
    {
        $this->update(['statut' => self::STATUT_EN_COURS]);
    }
}

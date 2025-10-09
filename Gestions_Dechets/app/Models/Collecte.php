<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collecte extends Model
{
    use HasFactory;

    // Constantes pour les statuts
    const STATUT_PREVUE = 'prevue';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_RATE = 'rate';
    const STATUT_ANNULE = 'annule';

    // Constantes pour les types de déchets collectés
    const TYPE_DECHET_MENAGER = 'dechet_menager';
    const TYPE_DECHET_VERT = 'dechet_vert';
    const TYPE_DECHET_ENCOMBRANT = 'encombrant';
    const TYPE_DECHET_DANGEREUX = 'dechet_dangereux';
    const TYPE_DECHET_RECYCLABLE = 'dechet_recyclable';

    // Constantes pour les unités de mesure
    const UNITE_KG = 'kg';
    const UNITE_LITRES = 'litres';
    const UNITE_UNITE = 'unite';
    const UNITE_M3 = 'm3';

    protected $fillable = [
        'itineraire_id',
        'point_collecte_id',
        'collecteur_id',
        'signalement_id',
        'type_dechet',
        'quantite',
        'unite_mesure',
        'statut',
        'date_collecte',
        'heure_debut',
        'heure_fin',
        'duree_collecte',
        'distance_collecte',
        'notes',
        'photo_avant',
        'photo_apres',
        'validation_gps',
        'latitude',
        'longitude',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date_collecte' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'quantite' => 'decimal:2',
        'duree_collecte' => 'integer',
        'distance_collecte' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'validation_gps' => 'boolean'
    ];

    // Relations
    public function itineraire()
    {
        return $this->belongsTo(Itineraire::class);
    }

    public function pointDeCollecte()
    {
        return $this->belongsTo(PointDeCollecte::class, 'point_collecte_id');
    }

    public function collecteur()
    {
        return $this->belongsTo(User::class, 'collecteur_id');
    }

    public function signalement()
    {
        return $this->belongsTo(Signalement::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    // Accesseurs pour les libellés
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            self::STATUT_PREVUE => 'Prévue',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Terminée',
            self::STATUT_RATE => 'Ratée',
            self::STATUT_ANNULE => 'Annulée',
            default => 'Non défini'
        };
    }

    public function getStatutClassAttribute()
    {
        return match($this->statut) {
            self::STATUT_PREVUE => 'badge bg-info',
            self::STATUT_EN_COURS => 'badge bg-primary',
            self::STATUT_TERMINE => 'badge bg-success',
            self::STATUT_RATE => 'badge bg-warning',
            self::STATUT_ANNULE => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    public function getTypeDechetLabelAttribute()
    {
        return match($this->type_dechet) {
            self::TYPE_DECHET_MENAGER => 'Déchets ménagers',
            self::TYPE_DECHET_VERT => 'Déchets verts',
            self::TYPE_DECHET_ENCOMBRANT => 'Encombrants',
            self::TYPE_DECHET_DANGEREUX => 'Déchets dangereux',
            self::TYPE_DECHET_RECYCLABLE => 'Recyclables',
            default => 'Non spécifié'
        };
    }

    public function getUniteMesureLabelAttribute()
    {
        return match($this->unite_mesure) {
            self::UNITE_KG => 'Kilogrammes',
            self::UNITE_LITRES => 'Litres',
            self::UNITE_UNITE => 'Unités',
            self::UNITE_M3 => 'Mètres cubes',
            default => 'Non spécifiée'
        };
    }

    // Méthodes utilitaires
    public function estEnCours()
    {
        return $this->statut === self::STATUT_EN_COURS;
    }

    public function estTerminee()
    {
        return $this->statut === self::STATUT_TERMINE;
    }

    public function estAnnulee()
    {
        return $this->statut === self::STATUT_ANNULE;
    }

    public function peutEtreModifiee()
    {
        return $this->statut === self::STATUT_PREVUE;
    }

    public function demarrer()
    {
        $this->update([
            'statut' => self::STATUT_EN_COURS,
            'heure_debut' => now()
        ]);
    }

    public function terminer()
    {
        $this->update([
            'statut' => self::STATUT_TERMINE,
            'heure_fin' => now(),
            'duree_collecte' => $this->calculerDuree()
        ]);
    }

    public function annuler($raison = null)
    {
        $this->update([
            'statut' => self::STATUT_ANNULE,
            'notes' => $raison ? $this->notes . "\nAnnulée: " . $raison : $this->notes
        ]);
    }

    public function marquerRatee($raison = null)
    {
        $this->update([
            'statut' => self::STATUT_RATE,
            'notes' => $raison ? $this->notes . "\nRatée: " . $raison : $this->notes
        ]);
    }

    public function calculerDuree()
    {
        if ($this->heure_debut && $this->heure_fin) {
            return $this->heure_debut->diffInMinutes($this->heure_fin);
        }
        return null;
    }

    public function calculerDistance()
    {
        // Logique pour calculer la distance parcourue
        return $this->distance_collecte;
    }

    public function validerGPS($latitude, $longitude)
    {
        $this->update([
            'validation_gps' => true,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
    }

    // Obtenir les statistiques d'une collecte
    public function getStatistiques()
    {
        return [
            'duree_collecte' => $this->duree_collecte,
            'distance_collecte' => $this->distance_collecte,
            'quantite_collectee' => $this->quantite,
            'type_dechet' => $this->type_dechet_label,
            'statut' => $this->statut_label,
            'validation_gps' => $this->validation_gps,
            'incidents' => $this->incidents,
        ];
    }
}

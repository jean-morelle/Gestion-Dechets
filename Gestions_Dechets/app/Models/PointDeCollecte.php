<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointDeCollecte extends Model
{
    use HasFactory;

    // Constantes pour les types de points de collecte
    const TYPE_PUBLIC = 'public';
    const TYPE_PRIVE = 'prive';
    const TYPE_INDUSTRIEL = 'industriel';
    const TYPE_COMMERCIAL = 'commercial';

    // Constantes pour les statuts
    const STATUT_ACTIF = 'actif';
    const STATUT_INACTIF = 'inactif';
    const STATUT_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'nom',
        'type',
        'adresse',
        'quartier',
        'latitude',
        'longitude',
        'capacite',
        'statut',
        'description',
        'horaires_ouverture',
        'horaires_fermeture',
        'contact_telephone',
        'contact_email',
        'responsable_nom',
        'responsable_telephone',
        'notes',
        'photo',
        'date_creation',
        'date_mise_a_jour',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'capacite' => 'integer',
        'date_creation' => 'datetime',
        'date_mise_a_jour' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function collectes()
    {
        return $this->hasMany(Collecte::class);
    }

    public function itinerairePoints()
    {
        return $this->hasMany(ItinerairePoint::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeParQuartier($query, $quartier)
    {
        return $query->where('quartier', $quartier);
    }

    public function scopeProcheDe($query, $latitude, $longitude, $rayonKm = 5)
    {
        return $query->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', $rayonKm)
                    ->orderBy('distance');
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        $types = [
            self::TYPE_PUBLIC => 'Public',
            self::TYPE_PRIVE => 'Privé',
            self::TYPE_INDUSTRIEL => 'Industriel',
            self::TYPE_COMMERCIAL => 'Commercial'
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getStatutLabelAttribute()
    {
        $statuts = [
            self::STATUT_ACTIF => 'Actif',
            self::STATUT_INACTIF => 'Inactif',
            self::STATUT_MAINTENANCE => 'En maintenance'
        ];

        return $statuts[$this->statut] ?? ucfirst($this->statut);
    }

    public function getStatutClassAttribute()
    {
        $classes = [
            self::STATUT_ACTIF => 'success',
            self::STATUT_INACTIF => 'secondary',
            self::STATUT_MAINTENANCE => 'warning'
        ];

        return $classes[$this->statut] ?? 'secondary';
    }

    public function getAdresseCompleteAttribute()
    {
        return $this->adresse . ', ' . $this->quartier;
    }

    public function getCoordonneesAttribute()
    {
        return $this->latitude . ', ' . $this->longitude;
    }

    // Méthodes utilitaires
    public function estActif()
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    public function estEnMaintenance()
    {
        return $this->statut === self::STATUT_MAINTENANCE;
    }

    public function calculerDistance($latitude, $longitude)
    {
        $earthRadius = 6371; // Rayon de la Terre en kilomètres

        $latDiff = deg2rad($latitude - $this->latitude);
        $lonDiff = deg2rad($longitude - $this->longitude);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function getStatistiques()
    {
        return [
            'total_collectes' => $this->collectes()->count(),
            'collectes_mois' => $this->collectes()->whereMonth('created_at', now()->month)->count(),
            'moyenne_collectes_semaine' => $this->collectes()->where('created_at', '>=', now()->subWeeks(4))->count() / 4,
            'derniere_collecte' => $this->collectes()->latest()->first()?->created_at,
        ];
    }
}
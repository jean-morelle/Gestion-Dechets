<?php

namespace App\Models;

use App\Support\Geo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointDeCollecte extends Model
{
    use HasFactory;

    const TYPE_PUBLIC = 'public';
    const TYPE_PRIVE = 'prive';
    const TYPE_INDUSTRIEL = 'industriel';
    const TYPE_COMMERCIAL = 'commercial';

    const STATUT_ACTIF = 'actif';
    const STATUT_INACTIF = 'inactif';
    const STATUT_MAINTENANCE = 'maintenance';

    const TYPES = [
        self::TYPE_PUBLIC => 'Bac public',
        self::TYPE_COMMERCIAL => 'Marché / commerce',
        self::TYPE_PRIVE => 'Privé (cour, résidence)',
        self::TYPE_INDUSTRIEL => 'Industriel',
    ];

    const STATUTS = [
        self::STATUT_ACTIF => 'Actif',
        self::STATUT_MAINTENANCE => 'En maintenance',
        self::STATUT_INACTIF => 'Inactif',
    ];

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
        'contact_responsable',
        'telephone',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'capacite' => 'integer',
    ];

    public function collectes()
    {
        return $this->hasMany(Collecte::class, 'point_collecte_id');
    }

    public function itineraires()
    {
        return $this->belongsToMany(Itineraire::class, 'itineraire_points')->withPivot('ordre');
    }

    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst((string) $this->type);
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? ucfirst((string) $this->statut);
    }

    /** Couleur de badge (classes tone-* de app.css) */
    public function getStatutToneAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_ACTIF => 'tone-green',
            self::STATUT_MAINTENANCE => 'tone-amber',
            default => 'tone-slate',
        };
    }

    public function distanceMetres(float $latitude, float $longitude): float
    {
        return Geo::distanceMetres($this->latitude, $this->longitude, $latitude, $longitude);
    }
}

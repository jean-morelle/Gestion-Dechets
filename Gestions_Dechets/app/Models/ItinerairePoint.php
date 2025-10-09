<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItinerairePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'itineraire_id',
        'point_de_collecte_id',
        'ordre'
    ];

    protected $casts = [
        'ordre' => 'integer'
    ];

    // Relations
    public function itineraire()
    {
        return $this->belongsTo(Itineraire::class);
    }

    public function pointDeCollecte()
    {
        return $this->belongsTo(PointDeCollecte::class);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre');
    }
}




























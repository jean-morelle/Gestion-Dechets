<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'collecteur_id',
        'itineraire_id',
        'type_incident',
        'description',
        'latitude',
        'longitude',
        'photo',
        'statut',
        'priorite',
        'admin_notes',
        'date_resolution'
    ];

    protected function casts(): array
    {
        return [
            'date_resolution' => 'datetime',
        ];
    }

    // Relations
    public function collecteur()
    {
        return $this->belongsTo(User::class, 'collecteur_id');
    }

    public function itineraire()
    {
        return $this->belongsTo(Itineraire::class);
    }

    // Utils
    public function isResolved()
    {
        return $this->statut === 'resolu';
    }

    public function isUrgent()
    {
        return $this->priorite === 'urgente';
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'panne_vehicule' => 'Panne de véhicule',
            'probleme_acces' => 'Problème d\'accès',
            'dechet_non_collectable' => 'Déchet non collectable',
            'autre' => 'Autre'
        ];
        return $labels[$this->type_incident] ?? ucfirst($this->type_incident);
    }

    public function getStatutLabelAttribute()
    {
        $labels = [
            'signale' => 'Signalé',
            'en_cours' => 'Pris en charge',
            'resolu' => 'Résolu',
            'annule' => 'Sans suite'
        ];
        return $labels[$this->statut] ?? ucfirst($this->statut);
    }

    public function getPrioriteLabelAttribute()
    {
        $labels = [
            'urgente' => 'Urgente',
            'elevee' => 'Élevée',
            'normale' => 'Normale',
            'faible' => 'Faible'
        ];
        return $labels[$this->priorite] ?? ucfirst($this->priorite);
    }

    public function getStatutClassAttribute()
    {
        return match($this->statut) {
            'signale' => 'warning',
            'en_cours' => 'info',
            'resolu' => 'success',
            'annule' => 'secondary',
            default => 'danger'
        };
    }

    public function getPrioriteClassAttribute()
    {
        return match($this->priorite) {
            'urgente' => 'danger',
            'elevee' => 'warning',
            'normale' => 'info',
            'faible' => 'secondary',
            default => 'info'
        };
    }
}

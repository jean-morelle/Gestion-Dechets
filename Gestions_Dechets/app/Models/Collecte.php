<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Passage d'un collecteur à un point de collecte, au cours d'une tournée.
 * Créée « prévue » au démarrage de la tournée, puis « terminée » (passage
 * validé avec photo) ou « ratée » (point non collecté, avec un motif).
 */
class Collecte extends Model
{
    use HasFactory;

    const STATUT_PREVUE = 'prevue';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_RATE = 'rate';
    const STATUT_ANNULE = 'annule';

    const TYPES_DECHET = [
        'dechet_menager' => 'Ordures ménagères',
        'dechet_recyclable' => 'Recyclables',
        'dechet_vert' => 'Déchets verts',
        'encombrant' => 'Encombrants',
        'dechet_dangereux' => 'Déchets dangereux',
    ];

    const MOTIFS_ECHEC = [
        'acces_impossible' => 'Accès impossible (rue bloquée, inondée…)',
        'point_vide' => 'Rien à collecter',
        'vehicule_plein' => 'Véhicule plein',
        'dechets_non_conformes' => 'Déchets non conformes (dangereux, mal triés)',
        'autre' => 'Autre raison',
    ];

    /** Au-delà de cet écart, le passage est enregistré mais signalé à l'administration */
    const TOLERANCE_GPS_METRES = 200;

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
        'latitude_fin',
        'longitude_fin',
        'precision_gps',
        'distance_point',
        'photo_validation',
        'notes',
        'motif_echec',
        'temps_collecte',
        'validation_gps',
    ];

    protected $casts = [
        'date_collecte' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'quantite' => 'decimal:2',
        'latitude_fin' => 'float',
        'longitude_fin' => 'float',
        'validation_gps' => 'boolean',
    ];

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

    public function estTraitee(): bool
    {
        return in_array($this->statut, [self::STATUT_TERMINE, self::STATUT_RATE, self::STATUT_ANNULE], true);
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_PREVUE => 'À faire',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Collecté',
            self::STATUT_RATE => 'Non collecté',
            self::STATUT_ANNULE => 'Annulé',
            default => 'Inconnu',
        };
    }

    /** Couleur de badge (classes tone-* de app.css) */
    public function getStatutToneAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_TERMINE => 'tone-green',
            self::STATUT_RATE => 'tone-red',
            self::STATUT_EN_COURS => 'tone-blue',
            self::STATUT_PREVUE => 'tone-amber',
            default => 'tone-slate',
        };
    }

    public function getTypeDechetLabelAttribute(): ?string
    {
        return self::TYPES_DECHET[$this->type_dechet] ?? null;
    }

    public function getMotifEchecLabelAttribute(): ?string
    {
        return self::MOTIFS_ECHEC[$this->motif_echec] ?? $this->motif_echec;
    }

    /** Le passage a-t-il été validé loin du point prévu ? */
    public function getEcartGpsSuspectAttribute(): bool
    {
        return $this->distance_point !== null && $this->distance_point > self::TOLERANCE_GPS_METRES;
    }
}

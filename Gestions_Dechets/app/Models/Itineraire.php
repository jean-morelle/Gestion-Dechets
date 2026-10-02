<?php

namespace App\Models;

use App\Support\Geo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Tournée de collecte : une suite ordonnée de points confiée à un collecteur.
 * planifiée → en cours (une collecte « prévue » par point) → terminée.
 */
class Itineraire extends Model
{
    use HasFactory;

    const TYPE_QUOTIDIEN = 'quotidien';
    const TYPE_HEBDOMADAIRE = 'hebdomadaire';
    const TYPE_MENSUEL = 'mensuel';
    const TYPE_PONCTUEL = 'ponctuel';

    const STATUT_PLANIFIE = 'planifie';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_ANNULE = 'annule';

    const TYPES = [
        self::TYPE_PONCTUEL => 'Ponctuelle',
        self::TYPE_QUOTIDIEN => 'Quotidienne',
        self::TYPE_HEBDOMADAIRE => 'Hebdomadaire',
        self::TYPE_MENSUEL => 'Mensuelle',
    ];

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
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'distance_estimee' => 'decimal:2',
        'duree_estimee' => 'integer',
    ];

    public function collecteur()
    {
        return $this->belongsTo(User::class, 'collecteur_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function collectes()
    {
        return $this->hasMany(Collecte::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function pointsDeCollecte()
    {
        return $this->belongsToMany(PointDeCollecte::class, 'itineraire_points')
            ->withPivot('ordre')
            ->withTimestamps()
            ->orderBy('itineraire_points.ordre');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Non défini';
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_PLANIFIE => 'Planifiée',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Terminée',
            self::STATUT_ANNULE => 'Annulée',
            default => 'Non défini',
        };
    }

    /** Couleur de badge (classes tone-* de app.css) */
    public function getStatutToneAttribute(): string
    {
        return match ($this->statut) {
            self::STATUT_PLANIFIE => 'tone-blue',
            self::STATUT_EN_COURS => 'tone-amber',
            self::STATUT_TERMINE => 'tone-green',
            default => 'tone-slate',
        };
    }

    /** Conservé pour les anciennes vues Bootstrap */
    public function getStatutClassAttribute(): string
    {
        return 'badge ' . $this->statut_tone;
    }

    public function peutEtreModifie(): bool
    {
        return $this->statut === self::STATUT_PLANIFIE;
    }

    /**
     * Remplace les étapes de la tournée, dans l'ordre donné, et recalcule la distance
     */
    public function definirEtapes(array $pointIds): void
    {
        $etapes = [];
        foreach (array_values($pointIds) as $i => $id) {
            $etapes[(int) $id] = ['ordre' => $i + 1];
        }
        $this->pointsDeCollecte()->sync($etapes);

        $points = PointDeCollecte::whereIn('id', array_keys($etapes))->get()->keyBy('id');
        $metres = 0;
        $precedent = null;
        foreach (array_keys($etapes) as $id) {
            $point = $points[$id];
            if ($precedent) {
                $metres += Geo::distanceMetres($precedent->latitude, $precedent->longitude, $point->latitude, $point->longitude);
            }
            $precedent = $point;
        }

        $this->update(['distance_estimee' => round($metres / 1000, 2)]);
    }

    /**
     * Démarrer la tournée : une collecte « prévue » est créée pour chaque étape
     */
    public function demarrer(): void
    {
        DB::transaction(function () {
            $this->update(['statut' => self::STATUT_EN_COURS]);

            $dejaCrees = $this->collectes()->pluck('point_collecte_id')->all();
            foreach ($this->pointsDeCollecte as $point) {
                if (in_array($point->id, $dejaCrees)) {
                    continue;
                }
                $this->collectes()->create([
                    'point_collecte_id' => $point->id,
                    'collecteur_id' => $this->collecteur_id,
                    'statut' => Collecte::STATUT_PREVUE,
                    'date_collecte' => today(),
                    'unite_mesure' => 'kg',
                ]);
            }
        });
    }

    /**
     * Clôturer la tournée : les étapes non traitées sont marquées « non collectées »
     */
    public function terminer(): void
    {
        DB::transaction(function () {
            $this->collectes()
                ->whereIn('statut', [Collecte::STATUT_PREVUE, Collecte::STATUT_EN_COURS])
                ->update([
                    'statut' => Collecte::STATUT_RATE,
                    'motif_echec' => 'autre',
                    'notes' => 'Étape non effectuée : tournée clôturée avant le passage.',
                    'heure_fin' => now(),
                ]);

            $this->update(['statut' => self::STATUT_TERMINE]);
        });
    }

    /**
     * Avancement de la tournée, à partir des collectes déjà chargées si possible
     */
    public function getProgressionAttribute(): array
    {
        $collectes = $this->relationLoaded('collectes') ? $this->collectes : $this->collectes()->get();
        $total = $this->points_de_collecte_count
            ?? ($this->relationLoaded('pointsDeCollecte') ? $this->pointsDeCollecte->count() : $this->pointsDeCollecte()->count());

        $collectees = $collectes->where('statut', Collecte::STATUT_TERMINE)->count();
        $ratees = $collectes->where('statut', Collecte::STATUT_RATE)->count();
        $faites = $collectees + $ratees;

        return [
            'total' => $total,
            'collectees' => $collectees,
            'ratees' => $ratees,
            'restantes' => max(0, $total - $faites),
            'pourcentage' => $total ? (int) round($faites / $total * 100) : 0,
            'kg' => (float) $collectes->where('statut', Collecte::STATUT_TERMINE)->sum('quantite'),
        ];
    }
}

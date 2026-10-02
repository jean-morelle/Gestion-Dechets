<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CalendrierCollecte extends Model
{
    use HasFactory;

    // Constantes pour les types de collecte
    const TYPE_MENAGERE = 'menagere';
    const TYPE_ENCOMBRANT = 'encombrant';
    const TYPE_VERT = 'vert';
    const TYPE_RECYCLAGE = 'recyclage';

    // Constantes pour les fréquences
    const FREQUENCE_QUOTIDIENNE = 'quotidienne';
    const FREQUENCE_HEBDOMADAIRE = 'hebdomadaire';
    const FREQUENCE_MENSUELLE = 'mensuelle';
    const FREQUENCE_PONCTUELLE = 'ponctuelle';

    // Constantes pour les jours de la semaine
    const LUNDI = 'lundi';
    const MARDI = 'mardi';
    const MERCREDI = 'mercredi';
    const JEUDI = 'jeudi';
    const VENDREDI = 'vendredi';
    const SAMEDI = 'samedi';
    const DIMANCHE = 'dimanche';

    /** Numéro ISO du jour (lundi = 1) */
    const JOURS_ISO = ['lundi' => 1, 'mardi' => 2, 'mercredi' => 3, 'jeudi' => 4, 'vendredi' => 5, 'samedi' => 6, 'dimanche' => 7];

    // Constantes pour les statuts
    const STATUT_ACTIF = 'actif';
    const STATUT_INACTIF = 'inactif';
    const STATUT_SUSPENDU = 'suspendu';

    protected $fillable = [
        'nom',
        'type_collecte',
        'quartier',
        'frequence',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'date_debut',
        'date_fin',
        'description',
        'statut',
        'responsable_id',
        'notes',
    ];

    protected $casts = [
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Relations avec les autres modèles
     */

    // Un calendrier de collecte a un responsable
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    // Un calendrier de collecte peut avoir plusieurs collectes
    public function collectes()
    {
        return $this->hasMany(Collecte::class);
    }

    /**
     * Méthodes utilitaires
     */

    // Vérifier si le calendrier est actif
    public function isActif()
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    // Vérifier si le calendrier est suspendu
    public function isSuspendu()
    {
        return $this->statut === self::STATUT_SUSPENDU;
    }

    // Obtenir le libellé du type de collecte
    public function getTypeCollecteLabelAttribute()
    {
        return match($this->type_collecte) {
            self::TYPE_MENAGERE => 'Ménagère',
            self::TYPE_ENCOMBRANT => 'Encombrant',
            self::TYPE_VERT => 'Vert',
            self::TYPE_RECYCLAGE => 'Recyclage',
            default => 'Non défini'
        };
    }

    // Obtenir le libellé de la fréquence
    public function getFrequenceLabelAttribute()
    {
        return match($this->frequence) {
            self::FREQUENCE_QUOTIDIENNE => 'Quotidienne',
            self::FREQUENCE_HEBDOMADAIRE => 'Hebdomadaire',
            self::FREQUENCE_MENSUELLE => 'Mensuelle',
            self::FREQUENCE_PONCTUELLE => 'Ponctuelle',
            default => 'Non définie'
        };
    }

    // Obtenir le libellé du jour de la semaine
    public function getJourSemaineLabelAttribute()
    {
        return match($this->jour_semaine) {
            self::LUNDI => 'Lundi',
            self::MARDI => 'Mardi',
            self::MERCREDI => 'Mercredi',
            self::JEUDI => 'Jeudi',
            self::VENDREDI => 'Vendredi',
            self::SAMEDI => 'Samedi',
            self::DIMANCHE => 'Dimanche',
            default => 'Non défini'
        };
    }

    // Obtenir le libellé du statut
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            self::STATUT_ACTIF => 'Actif',
            self::STATUT_INACTIF => 'Inactif',
            self::STATUT_SUSPENDU => 'Suspendu',
            default => 'Non défini'
        };
    }

    // Obtenir la classe CSS pour le statut
    public function getStatutClassAttribute()
    {
        return match($this->statut) {
            self::STATUT_ACTIF => 'badge bg-success',
            self::STATUT_INACTIF => 'badge bg-secondary',
            self::STATUT_SUSPENDU => 'badge bg-warning',
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
            self::TYPE_RECYCLAGE => 'badge bg-info',
            default => 'badge bg-light'
        };
    }

    // Scope pour les calendriers actifs
    public function scopeActifs($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    // Scope pour un quartier spécifique
    public function scopeQuartier($query, $quartier)
    {
        return $query->where('quartier', $quartier);
    }

    // Scope pour un type de collecte spécifique
    public function scopeTypeCollecte($query, $type)
    {
        return $query->where('type_collecte', $type);
    }

    /** « des ordures ménagères », pour les phrases : « collecte des ordures ménagères à Bè » */
    public function getTypeCollecteLabelCourtAttribute(): string
    {
        return match ($this->type_collecte) {
            self::TYPE_MENAGERE => 'des ordures ménagères',
            self::TYPE_ENCOMBRANT => 'des encombrants',
            self::TYPE_VERT => 'des déchets verts',
            self::TYPE_RECYCLAGE => 'des recyclables',
            default => 'des déchets',
        };
    }

    /** La date est-elle dans la période de validité (date de début / de fin) ? */
    public function isDateValide($date): bool
    {
        $jour = Carbon::parse($date)->toDateString();

        if ($this->date_debut && $jour < $this->date_debut->toDateString()) {
            return false;
        }
        if ($this->date_fin && $jour > $this->date_fin->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Y a-t-il un passage ce jour-là ?
     * Hebdomadaire : le jour de la semaine choisi. Mensuelle : le même quantième
     * que la date de début (le 1er si aucune). Ponctuelle : la date de début.
     */
    public function aLieuLe($date): bool
    {
        $date = Carbon::parse($date);

        if ($this->statut !== self::STATUT_ACTIF || ! $this->isDateValide($date)) {
            return false;
        }

        return match ($this->frequence) {
            self::FREQUENCE_QUOTIDIENNE => true,
            self::FREQUENCE_HEBDOMADAIRE => (self::JOURS_ISO[$this->jour_semaine] ?? null) === $date->dayOfWeekIso,
            self::FREQUENCE_MENSUELLE => $date->day === ($this->date_debut?->day ?? 1),
            self::FREQUENCE_PONCTUELLE => $this->date_debut?->isSameDay($date) ?? false,
            default => false,
        };
    }

    /** Prochain passage à partir d'aujourd'hui (inclus), dans les deux mois à venir */
    public function getProchaineDateCollecte(): ?Carbon
    {
        $jour = today();
        for ($i = 0; $i <= 62; $i++, $jour = $jour->copy()->addDay()) {
            if ($this->aLieuLe($jour)) {
                return $jour;
            }
        }

        return null;
    }
}

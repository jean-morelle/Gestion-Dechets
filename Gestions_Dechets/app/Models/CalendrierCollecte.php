<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Vérifier si une date est dans la période d'activité
    public function isDateValide($date)
    {
        if ($this->date_debut && $date < $this->date_debut) {
            return false;
        }
        
        if ($this->date_fin && $date > $this->date_fin) {
            return false;
        }
        
        return true;
    }

    // Obtenir la prochaine date de collecte
    public function getProchaineDateCollecte()
    {
        if (!$this->isActif()) {
            return null;
        }

        $aujourdhui = now();
        
        if (!$this->isDateValide($aujourdhui)) {
            return null;
        }

        switch ($this->frequence) {
            case self::FREQUENCE_QUOTIDIENNE:
                return $aujourdhui->addDay();
                
            case self::FREQUENCE_HEBDOMADAIRE:
                if ($this->jour_semaine) {
                    $jourSemaine = match($this->jour_semaine) {
                        self::LUNDI => 1,
                        self::MARDI => 2,
                        self::MERCREDI => 3,
                        self::JEUDI => 4,
                        self::VENDREDI => 5,
                        self::SAMEDI => 6,
                        self::DIMANCHE => 0,
                        default => null
                    };
                    
                    if ($jourSemaine !== null) {
                        $prochaineDate = $aujourdhui->next($jourSemaine);
                        return $this->isDateValide($prochaineDate) ? $prochaineDate : null;
                    }
                }
                break;
                
            case self::FREQUENCE_MENSUELLE:
                return $aujourdhui->addMonth();
                
            case self::FREQUENCE_PONCTUELLE:
                return $this->date_debut;
        }
        
        return null;
    }
}










































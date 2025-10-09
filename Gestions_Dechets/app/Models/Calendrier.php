<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Calendrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'quartier',
        'type_collecte',
        'frequence',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'date_debut',
        'date_fin',
        'description',
        'statut'
    ];

    protected $casts = [
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // Relation avec les rappels
    public function rappels()
    {
        return $this->hasMany(Rappel::class);
    }

    // Scope pour les calendriers actifs
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    // Scope pour un quartier spécifique
    public function scopePourQuartier($query, $quartier)
    {
        return $query->where('quartier', $quartier);
    }

    // Scope pour un type de collecte spécifique
    public function scopePourType($query, $type)
    {
        return $query->where('type_collecte', $type);
    }

    // Obtenir la prochaine collecte
    public function getProchaineCollecte()
    {
        $now = now();
        
        // Si c'est une collecte ponctuelle
        if ($this->frequence === 'ponctuelle') {
            if ($this->date_debut && $this->date_debut >= $now->toDateString()) {
                return Carbon::parse($this->date_debut . ' ' . $this->heure_debut->format('H:i:s'));
            }
            return null;
        }

        // Si c'est une collecte récurrente
        $prochaineDate = $this->calculerProchaineDate();
        if ($prochaineDate) {
            return Carbon::parse($prochaineDate . ' ' . $this->heure_debut->format('H:i:s'));
        }

        return null;
    }

    // Calculer la prochaine date de collecte
    private function calculerProchaineDate()
    {
        $now = now();
        $jourSemaine = $this->jour_semaine;
        
        if (!$jourSemaine || $jourSemaine === 'Non défini') {
            return null;
        }

        // Convertir le jour de la semaine en numéro
        $joursSemaine = [
            'lundi' => 1,
            'mardi' => 2,
            'mercredi' => 3,
            'jeudi' => 4,
            'vendredi' => 5,
            'samedi' => 6,
            'dimanche' => 0
        ];

        $jourCible = $joursSemaine[$jourSemaine] ?? null;
        if ($jourCible === null) {
            return null;
        }

        // Calculer la prochaine occurrence
        $prochaineDate = $now->copy()->next($jourCible);
        
        // Vérifier les contraintes de date
        if ($this->date_debut && $prochaineDate->toDateString() < $this->date_debut) {
            $prochaineDate = Carbon::parse($this->date_debut)->next($jourCible);
        }
        
        if ($this->date_fin && $prochaineDate->toDateString() > $this->date_fin) {
            return null;
        }

        return $prochaineDate->toDateString();
    }

    // Obtenir le label du type de collecte
    public function getTypeCollecteLabelAttribute()
    {
        $types = [
            'menagere' => 'Ménagère',
            'encombrant' => 'Encombrant',
            'vert' => 'Vert',
            'recyclage' => 'Recyclage'
        ];

        return $types[$this->type_collecte] ?? ucfirst($this->type_collecte);
    }

    // Obtenir le label de la fréquence
    public function getFrequenceLabelAttribute()
    {
        $frequences = [
            'quotidienne' => 'Quotidienne',
            'hebdomadaire' => 'Hebdomadaire',
            'mensuelle' => 'Mensuelle',
            'ponctuelle' => 'Ponctuelle'
        ];

        return $frequences[$this->frequence] ?? ucfirst($this->frequence);
    }
}
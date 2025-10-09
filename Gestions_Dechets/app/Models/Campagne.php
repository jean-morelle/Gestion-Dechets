<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Campagne extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'type',
        'fichier',
        'url_video',
        'contenu_message',
        'image_preview',
        'statut',
        'date_debut',
        'date_fin',
        'quartiers_cibles',
        'vues',
        'partages'
    ];

    protected $casts = [
        'quartiers_cibles' => 'array',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // Scope pour les campagnes actives
    public function scopeActive($query)
    {
        return $query->where('statut', 'active');
    }

    // Scope pour les campagnes visibles (actives et dans la période)
    public function scopeVisible($query)
    {
        $now = now()->toDateString();
        return $query->where('statut', 'active')
                    ->where(function ($q) use ($now) {
                        $q->whereNull('date_debut')
                          ->orWhere('date_debut', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('date_fin')
                          ->orWhere('date_fin', '>=', $now);
                    });
    }

    // Scope pour un type spécifique
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope pour un quartier spécifique
    public function scopePourQuartier($query, $quartier)
    {
        return $query->where(function ($q) use ($quartier) {
            $q->whereNull('quartiers_cibles')
              ->orWhereJsonContains('quartiers_cibles', $quartier);
        });
    }

    // Vérifier si la campagne est visible
    public function estVisible()
    {
        if ($this->statut !== 'active') {
            return false;
        }

        $now = now()->toDateString();
        
        if ($this->date_debut && $this->date_debut > $now) {
            return false;
        }

        if ($this->date_fin && $this->date_fin < $now) {
            return false;
        }

        return true;
    }

    // Vérifier si la campagne cible un quartier
    public function cibleQuartier($quartier)
    {
        if (!$this->quartiers_cibles) {
            return true; // Si pas de quartiers spécifiés, cible tous
        }

        return in_array($quartier, $this->quartiers_cibles);
    }

    // Incrémenter le nombre de vues
    public function incrementerVues()
    {
        $this->increment('vues');
    }

    // Incrémenter le nombre de partages
    public function incrementerPartages()
    {
        $this->increment('partages');
    }

    // Obtenir le label du type
    public function getTypeLabelAttribute()
    {
        $types = [
            'affiche' => 'Affiche numérique',
            'video' => 'Vidéo',
            'message' => 'Message',
            'infographie' => 'Infographie'
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    // Obtenir le label du statut
    public function getStatutLabelAttribute()
    {
        $statuts = [
            'brouillon' => 'Brouillon',
            'active' => 'Active',
            'terminee' => 'Terminée',
            'archivee' => 'Archivée'
        ];

        return $statuts[$this->statut] ?? ucfirst($this->statut);
    }

    // Obtenir la classe CSS du statut
    public function getStatutClassAttribute()
    {
        $classes = [
            'brouillon' => 'secondary',
            'active' => 'success',
            'terminee' => 'info',
            'archivee' => 'dark'
        ];

        return $classes[$this->statut] ?? 'secondary';
    }

    // Obtenir l'URL de la ressource
    public function getUrlRessourceAttribute()
    {
        switch ($this->type) {
            case 'video':
                return $this->url_video;
            case 'affiche':
            case 'infographie':
                return $this->fichier ? asset('storage/' . $this->fichier) : null;
            default:
                return null;
        }
    }

    // Obtenir le contenu à afficher
    public function getContenuAAfficherAttribute()
    {
        switch ($this->type) {
            case 'message':
                return $this->contenu_message;
            case 'video':
                return $this->url_video;
            case 'affiche':
            case 'infographie':
                return $this->fichier;
            default:
                return null;
        }
    }
}
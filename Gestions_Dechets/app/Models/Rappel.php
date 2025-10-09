<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rappel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendrier_id',
        'type_rappel',
        'delai_heures',
        'actif',
        'derniere_envoi'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'derniere_envoi' => 'datetime',
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le calendrier
    public function calendrier()
    {
        return $this->belongsTo(Calendrier::class);
    }

    // Scope pour les rappels actifs
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    // Scope pour les rappels par type
    public function scopeParType($query, $type)
    {
        return $query->where('type_rappel', $type);
    }

    // Scope pour les rappels d'un utilisateur
    public function scopePourUtilisateur($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Vérifier si le rappel doit être envoyé
    public function doitEtreEnvoye()
    {
        if (!$this->actif) {
            return false;
        }

        // Vérifier si le rappel a déjà été envoyé aujourd'hui
        if ($this->derniere_envoi && $this->derniere_envoi->isToday()) {
            return false;
        }

        // Vérifier si c'est le bon moment pour envoyer le rappel
        $prochaineCollecte = $this->calendrier->getProchaineCollecte();
        if (!$prochaineCollecte) {
            return false;
        }

        $heuresAvantCollecte = now()->diffInHours($prochaineCollecte, false);
        return $heuresAvantCollecte <= $this->delai_heures && $heuresAvantCollecte > 0;
    }

    // Marquer comme envoyé
    public function marquerCommeEnvoye()
    {
        $this->update(['derniere_envoi' => now()]);
    }
}
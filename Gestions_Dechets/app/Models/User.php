<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Constantes pour les rôles
    const ROLE_CITOYEN = 'citoyen';
    const ROLE_COLLECTEUR = 'collecteur';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'telephone',
        'adresse',
        'quartier',
        'photo',
        'statut',
        'theme',
        'language',
        'notification_preferences',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Relations avec les autres modèles
     */

    // Un utilisateur peut faire plusieurs signalements
    public function signalements()
    {
        return $this->hasMany(Signalement::class);
    }

    // Un utilisateur peut faire plusieurs plaintes
    public function plaintes()
    {
        return $this->hasMany(Plainte::class);
    }


    // Un utilisateur peut recevoir plusieurs notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    // Un collecteur peut avoir plusieurs itinéraires
    public function itineraires()
    {
        return $this->hasMany(Itineraire::class, 'collecteur_id');
    }

    // Un collecteur peut effectuer plusieurs collectes
    public function collectes()
    {
        return $this->hasMany(Collecte::class, 'collecteur_id');
    }

    // Un collecteur peut signaler plusieurs incidents
    public function incidents()
    {
        return $this->hasMany(Incident::class, 'collecteur_id');
    }

    // Un citoyen peut faire plusieurs demandes de collecte
    public function demandesCollecte()
    {
        return $this->hasMany(DemandeCollecte::class);
    }

    /**
     * Méthodes utilitaires pour vérifier les rôles
     */
    public function isCitoyen()
    {
        return $this->role === self::ROLE_CITOYEN;
    }

    public function isCollecteur()
    {
        return $this->role === self::ROLE_COLLECTEUR;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }
    /**
     * Vérifier si l'utilisateur est actif
     */
    public function isActive()
    {
        return $this->statut === 'actif';
    }
}




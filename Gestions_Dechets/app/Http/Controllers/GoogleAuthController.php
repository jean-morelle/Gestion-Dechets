<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Rediriger vers Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Gérer le callback de Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Vérifier si l'utilisateur existe déjà
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                if ($user->statut !== 'actif') {
                    return redirect()->route('login')->withErrors(['email' => 'Ce compte est suspendu ou désactivé. Contactez le service de la mairie.']);
                }
                Auth::login($user);
            } else {
                // Créer un nouvel utilisateur
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'role' => 'citoyen', // Rôle par défaut
                    'statut' => 'actif',
                    'password' => Hash::make(Str::random(40)), // inconnu de l'utilisateur : il se connecte via Google
                    'mot_de_passe_defini' => false,
                ]);
                
                Auth::login($user);
            }
            
            // Rediriger vers le dashboard selon le rôle
            return $this->redirectToDashboard($user->role);
            
        } catch (\Exception $e) {
            // Le détail technique va dans les journaux, pas à l'écran
            report($e);

            return redirect()->route('login')->with('error', 'La connexion avec Google a échoué. Réessayez ou utilisez votre e-mail et mot de passe.');
        }
    }

    /**
     * Rediriger vers le dashboard selon le rôle
     */
    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 'citoyen':
                return redirect()->route('citoyen.dashboard');
            case 'collecteur':
                return redirect()->route('collecteur.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('citoyen.dashboard');
        }
    }
}




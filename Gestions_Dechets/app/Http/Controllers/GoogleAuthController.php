<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                // L'utilisateur existe, se connecter
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
                    'password' => bcrypt(str_random(16)), // Mot de passe aléatoire
                ]);
                
                Auth::login($user);
            }
            
            // Rediriger vers le dashboard selon le rôle
            return $this->redirectToDashboard($user->role);
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Erreur lors de la connexion avec Google: ' . $e->getMessage());
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




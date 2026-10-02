<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /** Tentatives autorisées par adresse e-mail et par IP avant blocage temporaire */
    const TENTATIVES_MAX = 5;
    const BLOCAGE_SECONDES = 300;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $cle = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($cle, self::TENTATIVES_MAX)) {
            $minutes = (int) ceil(RateLimiter::availableIn($cle) / 60);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives de connexion. Réessayez dans {$minutes} minute" . ($minutes > 1 ? 's' : '') . ' ou utilisez « Mot de passe oublié ».',
            ]);
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($cle, self::BLOCAGE_SECONDES);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($cle);

        $user = Auth::user();

        // Compte suspendu ou désactivé par l'administration
        if ($user->statut !== 'actif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Ce compte est ' . ($user->statut === 'suspendu' ? 'suspendu' : 'désactivé') . '. Contactez le service de la mairie.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(match ($user->role) {
            'collecteur' => route('collecteur.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('citoyen.dashboard'),
        });
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

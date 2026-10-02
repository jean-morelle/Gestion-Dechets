<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tant qu'un compte utilise le mot de passe provisoire donné par
 * l'administration, seule la page de changement de mot de passe est accessible.
 */
class ExigerNouveauMotDePasse
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->doit_changer_mot_de_passe && ! $request->routeIs('settings.index', 'settings.password', 'logout')) {
            return redirect()->to(route('settings.index') . '#securite')
                ->with('warning', 'Bienvenue ! Choisissez votre mot de passe personnel pour continuer.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur a le rôle requis
        if ($user->role !== $role) {
            abort(403, 'Accès non autorisé. Rôle requis: ' . $role);
        }

        // Vérifier si l'utilisateur est actif
        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Votre compte a été désactivé.');
        }

        return $next($request);
    }
}


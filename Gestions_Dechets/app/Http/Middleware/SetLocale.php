<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Ordre de priorité pour la langue :
        // 1. Session
        // 2. Préférence utilisateur en base (si implémenté)
        // 3. Langue par défaut
        
        $locale = Session::get('language', config('app.locale', 'fr'));
        
        // Vérifier que la langue est supportée
        if (in_array($locale, ['fr', 'en', 'ee'])) {
            App::setLocale($locale);
        } else {
            App::setLocale('fr');
        }
        
        return $next($request);
    }
}







































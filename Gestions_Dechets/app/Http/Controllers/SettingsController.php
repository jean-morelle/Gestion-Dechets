<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        return view('settings.index');
    }

    /**
     * Mettre à jour les préférences d'apparence
     */
    public function updateAppearance(Request $request)
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark'],
            'language' => ['required', 'in:fr,en,ee'],
        ]);

        // Sauvegarder les préférences en session
        Session::put('theme', $request->theme);
        Session::put('language', $request->language);
        
        // Appliquer la langue immédiatement
        app()->setLocale($request->language);

        return redirect()->route('settings.index')->with('success', 'Vos paramètres ont été sauvegardés avec succès !');
    }

    /**
     * Mettre à jour les préférences de notifications
     */
    public function updateNotifications(Request $request)
    {
        // Pas de validation stricte car les checkboxes envoient "on" ou rien
        // On utilise has() pour vérifier si le champ est présent
        
        // Sauvegarder les préférences en session
        Session::put('notifications_email', $request->has('notifications_email'));
        Session::put('notifications_sms', $request->has('notifications_sms'));
        Session::put('notifications_push', $request->has('notifications_push'));

        return redirect()->route('settings.index')->with('success', 'Vos préférences de notifications ont été mises à jour !');
    }
}

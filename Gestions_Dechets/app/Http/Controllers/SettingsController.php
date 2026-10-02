<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /** Couleur principale de l'interface (voir [data-accent] dans app.css) */
    const COULEURS = [
        'vert' => 'Vert CollectPlus',
        'bleu' => 'Bleu lagune',
        'ocre' => 'Ocre',
        'violet' => 'Violet',
    ];

    const TAILLES = [
        'normale' => 'Normale',
        'grande' => 'Grande',
        'tres-grande' => 'Très grande',
    ];

    /**
     * Afficher la page des paramètres
     */
    public function index(Request $request)
    {
        return view('settings.index', [
            'user' => $request->user(),
            'appareils' => $this->appareilsConnectes($request),
        ]);
    }

    /**
     * Enregistrer le thème sur le compte (il suit l'utilisateur d'un appareil à l'autre)
     */
    public function updateAppearance(Request $request)
    {
        $donnees = $request->validate([
            'theme' => ['required', 'in:light,dark,auto'],
            'couleur_accent' => ['required', 'in:' . implode(',', array_keys(self::COULEURS))],
            'taille_texte' => ['required', 'in:' . implode(',', array_keys(self::TAILLES))],
        ]);

        $request->user()->update($donnees);

        return redirect()->route('settings.index')->with('success', 'Apparence enregistrée.');
    }

    /**
     * Bouton clair/sombre de la barre du haut : bascule sans quitter la page
     */
    public function basculerTheme(Request $request)
    {
        $donnees = $request->validate(['theme' => ['required', 'in:light,dark']]);

        $request->user()->update($donnees);

        return $request->expectsJson() ? response()->noContent() : back();
    }

    /**
     * Préférences de notification (rappel de collecte la veille)
     */
    public function updateNotifications(Request $request)
    {
        $user = $request->user();
        $preferences = $user->notification_preferences ?? [];
        $preferences['rappel_collecte'] = $request->boolean('rappel_collecte');

        $user->update(['notification_preferences' => $preferences]);

        return redirect()->route('settings.index')->with('success', $preferences['rappel_collecte']
            ? 'Vous serez prévenu la veille des collectes dans votre quartier.'
            : 'Rappels de collecte désactivés.');
    }

    /**
     * Droit d'accès : toutes les données du compte, dans un fichier JSON
     */
    public function exporterDonnees(Request $request)
    {
        $user = $request->user();
        $sans = ['user_id', 'updated_at'];

        $donnees = [
            'export_du' => now()->toIso8601String(),
            'compte' => $user->only(['name', 'email', 'telephone', 'adresse', 'quartier', 'role', 'created_at', 'theme', 'notification_preferences']),
            'signalements' => $user->signalements()->get()->map->makeHidden($sans),
            'demandes_de_collecte' => $user->demandesCollecte()->get()->map->makeHidden($sans),
            'plaintes' => $user->plaintes()->get()->map->makeHidden($sans),
            'notifications' => $user->notifications()->get(['titre', 'message', 'statut', 'created_at']),
        ];
        if ($user->role === 'collecteur') {
            $donnees['passages'] = $user->collectes()->get()->map->makeHidden(['collecteur_id']);
            $donnees['incidents'] = $user->incidents()->get()->map->makeHidden(['collecteur_id']);
        }

        return response()->streamDownload(
            fn () => print(json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            'mes-donnees-collectplus-' . now()->format('Y-m-d') . '.json',
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    /**
     * Droit à l'effacement (citoyens) : le compte est anonymisé. Les signalements
     * restent dans les statistiques du service mais ne sont plus rattachés à une personne.
     */
    public function supprimerCompte(Request $request)
    {
        $user = $request->user();
        abort_unless($user->role === 'citoyen', 403, 'Les comptes des agents sont gérés par l’administration.');

        if (self::aUnMotDePasse($user)) {
            $request->validate(
                ['password_suppression' => ['required', 'current_password']],
                ['password_suppression.current_password' => 'Le mot de passe est incorrect.'],
                ['password_suppression' => 'mot de passe']
            );
        } else {
            $request->validate(
                ['confirmation' => ['required', 'in:SUPPRIMER']],
                ['confirmation.in' => 'Tapez SUPPRIMER en majuscules pour confirmer.']
            );
        }

        DB::transaction(function () use ($user) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            // Coordonnées retirées des demandes conservées pour le suivi du service
            $user->demandesCollecte()->update(['contact_telephone' => null]);
            $user->plaintes()->update(['contact_telephone' => null]);
            $user->notifications()->delete();
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();

            $user->forceFill([
                'name' => 'Ancien utilisateur',
                'email' => 'compte-supprime-' . $user->id . '@invalide.local',
                'telephone' => null,
                'adresse' => null,
                'quartier' => null,
                'photo' => null,
                'google_id' => null,
                'password' => Hash::make(Str::random(40)),
                'remember_token' => null,
                'notification_preferences' => null,
                'statut' => 'inactif',
            ])->save();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Votre compte a été supprimé. Merci d’avoir contribué à la propreté de la ville.');
    }

    /**
     * Modifier le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $regles = ['password' => ['required', 'confirmed', Password::defaults()]];
        if (self::aUnMotDePasse($request->user())) {
            $regles['current_password'] = ['required', 'current_password'];
        }

        $request->validate($regles, [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
        ], [
            'current_password' => 'mot de passe actuel',
            'password' => 'nouveau mot de passe',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'mot_de_passe_defini' => true,
            'doit_changer_mot_de_passe' => false,
        ]);

        return redirect()->to(route('settings.index') . '#securite')->with('success', 'Mot de passe modifié.');
    }

    /**
     * Déconnecter tous les autres appareils
     */
    public function destroyOtherSessions(Request $request)
    {
        $user = $request->user();

        if ($this->aUnMotDePasse($user)) {
            $request->validate(
                ['password_session' => ['required', 'current_password']],
                ['password_session.current_password' => 'Le mot de passe est incorrect.'],
                ['password_session' => 'mot de passe']
            );
            // Invalide aussi les cookies « Se souvenir de moi » des autres appareils
            Auth::logoutOtherDevices($request->password_session);
        }

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return redirect()->to(route('settings.index') . '#securite')->with('success', 'Les autres appareils ont été déconnectés.');
    }

    /**
     * Un compte créé via Google reçoit un mot de passe aléatoire que
     * l'utilisateur ne connaît pas : on ne le lui demande pas tant
     * qu'il n'en a pas choisi un.
     */
    public static function aUnMotDePasse($user): bool
    {
        return (bool) $user->mot_de_passe_defini;
    }

    /**
     * Sessions ouvertes par l'utilisateur (uniquement avec le driver « database »)
     */
    private function appareilsConnectes(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return DB::table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(fn ($session) => (object) [
                'navigateur' => $this->navigateur($session->user_agent ?? ''),
                'systeme' => $this->systeme($session->user_agent ?? ''),
                'mobile' => (bool) preg_match('/Mobile|Android|iPhone|iPad/i', $session->user_agent ?? ''),
                'ip' => $session->ip_address,
                'actuel' => $session->id === $request->session()->getId(),
                'derniere_activite' => Carbon::createFromTimestamp($session->last_activity),
            ]);
    }

    private function navigateur(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') => 'Opera',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Chrome/') => 'Chrome',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Navigateur inconnu',
        };
    }

    private function systeme(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Android') => 'Android',
            (bool) preg_match('/iPhone|iPad/', $ua) => 'iOS',
            str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Système inconnu',
        };
    }
}

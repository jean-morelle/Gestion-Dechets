<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RegleMotDePasse;

/**
 * Réinitialisation du mot de passe par e-mail (lien à usage unique, valable 60 minutes)
 */
class MotDePasseOublieController extends Controller
{
    public function demande()
    {
        return view('auth.mot-de-passe-oublie');
    }

    public function envoyerLien(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $statut = Password::sendResetLink($request->only('email'));

        if ($statut === Password::RESET_THROTTLED) {
            return back()->withInput()->withErrors(['email' => 'Un e-mail vient déjà d’être envoyé. Patientez une minute avant de recommencer.']);
        }

        // Même réponse que l'adresse existe ou non : on ne révèle pas qui a un compte
        return back()->with('success', 'Si un compte correspond à cette adresse, un e-mail contenant un lien de réinitialisation vient d’être envoyé. Pensez à vérifier les courriers indésirables.');
    }

    public function formulaire(Request $request, string $token)
    {
        return view('auth.reinitialiser-mot-de-passe', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reinitialiser(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', RegleMotDePasse::defaults()],
        ], [], ['password' => 'mot de passe']);

        $statut = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $motDePasse) {
                $user->forceFill([
                    'password' => Hash::make($motDePasse),
                    'remember_token' => Str::random(60),
                    'mot_de_passe_defini' => true,
                    'doit_changer_mot_de_passe' => false,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($statut !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Ce lien n’est plus valable (déjà utilisé ou expiré). Faites une nouvelle demande.']);
        }

        return redirect()->route('login')->with('success', 'Mot de passe modifié. Vous pouvez vous connecter.');
    }
}

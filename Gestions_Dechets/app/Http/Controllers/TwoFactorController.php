<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Afficher la page de gestion 2FA
     */
    public function show()
    {
        $user = Auth::user();
        
        return view('two-factor.index', compact('user'));
    }

    /**
     * Activer la 2FA
     */
    public function enable()
    {
        $user = Auth::user();
        
        if ($user->two_factor_enabled) {
            return redirect()->route('two-factor.index')
                ->with('info', 'L\'authentification à deux facteurs est déjà activée.');
        }

        // Générer la clé secrète
        $secret = $this->twoFactorService->generateSecret();
        $user->two_factor_secret = $secret;
        $user->save();

        // Générer le QR code
        $qrCodeUrl = $this->twoFactorService->generateQRCodeUrl($user->email, $secret);

        return view('two-factor.enable', compact('secret', 'qrCodeUrl'));
    }

    /**
     * Confirmer l'activation de la 2FA
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        
        if ($this->twoFactorService->verifyCode($user->two_factor_secret, $request->code)) {
            $user->two_factor_enabled = true;
            $user->two_factor_confirmed_at = now();
            $user->save();

            // Générer les codes de récupération
            $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
            $user->two_factor_recovery_codes = json_encode($recoveryCodes);
            $user->save();

            return view('two-factor.recovery-codes', compact('recoveryCodes'))
                ->with('success', 'Authentification à deux facteurs activée avec succès !');
        }

        return redirect()->back()->withErrors(['code' => 'Code de vérification invalide.']);
    }

    /**
     * Désactiver la 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('two-factor.index')
            ->with('success', 'Authentification à deux facteurs désactivée.');
    }

    /**
     * Afficher les codes de récupération
     */
    public function showRecoveryCodes()
    {
        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('two-factor.index')
                ->with('error', 'L\'authentification à deux facteurs n\'est pas activée.');
        }

        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true) ?? [];

        return view('two-factor.recovery-codes', compact('recoveryCodes'));
    }

    /**
     * Régénérer les codes de récupération
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        
        if (!$user->two_factor_enabled) {
            return redirect()->route('two-factor.index')
                ->with('error', 'L\'authentification à deux facteurs n\'est pas activée.');
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
        $user->two_factor_recovery_codes = json_encode($recoveryCodes);
        $user->save();

        return view('two-factor.recovery-codes', compact('recoveryCodes'))
            ->with('success', 'Nouveaux codes de récupération générés.');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function edit()
    {
        $user = Auth::user();

        // Activité réelle affichée sous l'identité, selon le rôle
        $activite = match ($user->role) {
            'citoyen' => [
                'Signalements' => $user->signalements()->count(),
                'Demandes de collecte' => $user->demandesCollecte()->count(),
            ],
            'collecteur' => [
                'Collectes terminées' => $user->collectes()->where('statut', Collecte::STATUT_TERMINE)->count(),
                'Incidents déclarés' => $user->incidents()->count(),
            ],
            default => [],
        };

        // Informations utiles aux équipes de terrain encore manquantes
        $aCompleter = collect([
            'telephone' => 'votre numéro de téléphone',
            'quartier' => 'votre quartier',
            'adresse' => 'votre adresse',
        ])->filter(fn ($libelle, $champ) => blank($user->$champ));

        return view('profile.edit', compact('user', 'activite', 'aCompleter'));
    }

    /**
     * Mettre à jour les informations personnelles
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'quartier' => ['nullable', 'string', 'max:100'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profils', 'public');
        } else {
            unset($data['photo']);
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profil mis à jour.');
    }

    /**
     * Retirer la photo de profil
     */
    public function destroyPhoto()
    {
        $user = Auth::user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
            $user->update(['photo' => null]);
        }

        return redirect()->route('profile.edit')->with('success', 'Photo retirée.');
    }
}

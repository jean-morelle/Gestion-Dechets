<?php

namespace App\Http\Controllers;

use App\Models\Rappel;
use App\Models\Calendrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RappelController extends Controller
{
    /**
     * Afficher les rappels de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();
        
        $rappels = Rappel::with(['calendrier'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('citoyen.rappels.index', compact('rappels'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $user = Auth::user();
        
        // Récupérer les calendriers disponibles pour le quartier de l'utilisateur
        $calendriers = Calendrier::actif()
            ->pourQuartier($user->quartier)
            ->get();

        return view('citoyen.rappels.create', compact('calendriers'));
    }

    /**
     * Enregistrer un nouveau rappel
     */
    public function store(Request $request)
    {
        $request->validate([
            'calendrier_id' => 'required|exists:calendriers,id',
            'type_rappel' => 'required|in:email,sms,push',
            'delai_heures' => 'required|integer|min:1|max:168', // Max 1 semaine
        ]);

        // Vérifier qu'il n'existe pas déjà un rappel pour ce calendrier et ce type
        $rappelExistant = Rappel::where('user_id', Auth::id())
            ->where('calendrier_id', $request->calendrier_id)
            ->where('type_rappel', $request->type_rappel)
            ->first();

        if ($rappelExistant) {
            return redirect()->back()
                ->withErrors(['type_rappel' => 'Un rappel de ce type existe déjà pour ce calendrier.'])
                ->withInput();
        }

        Rappel::create([
            'user_id' => Auth::id(),
            'calendrier_id' => $request->calendrier_id,
            'type_rappel' => $request->type_rappel,
            'delai_heures' => $request->delai_heures,
        ]);

        return redirect()->route('citoyen.rappels.index')
            ->with('success', 'Rappel configuré avec succès !');
    }

    /**
     * Afficher un rappel spécifique
     */
    public function show(Rappel $rappel)
    {
        // Vérifier que l'utilisateur peut voir ce rappel
        if ($rappel->user_id !== Auth::id()) {
            abort(403);
        }

        $rappel->load(['calendrier']);

        return view('citoyen.rappels.show', compact('rappel'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Rappel $rappel)
    {
        // Vérifier que l'utilisateur peut modifier ce rappel
        if ($rappel->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $calendriers = Calendrier::actif()
            ->pourQuartier($user->quartier)
            ->get();

        return view('citoyen.rappels.edit', compact('rappel', 'calendriers'));
    }

    /**
     * Mettre à jour un rappel
     */
    public function update(Request $request, Rappel $rappel)
    {
        // Vérifier que l'utilisateur peut modifier ce rappel
        if ($rappel->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'calendrier_id' => 'required|exists:calendriers,id',
            'type_rappel' => 'required|in:email,sms,push',
            'delai_heures' => 'required|integer|min:1|max:168',
            'actif' => 'boolean',
        ]);

        $rappel->update($request->only(['calendrier_id', 'type_rappel', 'delai_heures', 'actif']));

        return redirect()->route('citoyen.rappels.index')
            ->with('success', 'Rappel mis à jour avec succès !');
    }

    /**
     * Supprimer un rappel
     */
    public function destroy(Rappel $rappel)
    {
        // Vérifier que l'utilisateur peut supprimer ce rappel
        if ($rappel->user_id !== Auth::id()) {
            abort(403);
        }

        $rappel->delete();

        return redirect()->route('citoyen.rappels.index')
            ->with('success', 'Rappel supprimé avec succès !');
    }

    /**
     * Basculer l'état actif/inactif d'un rappel
     */
    public function toggle(Rappel $rappel)
    {
        // Vérifier que l'utilisateur peut modifier ce rappel
        if ($rappel->user_id !== Auth::id()) {
            abort(403);
        }

        $rappel->update(['actif' => !$rappel->actif]);

        $message = $rappel->actif ? 'Rappel activé' : 'Rappel désactivé';

        return redirect()->back()
            ->with('success', $message . ' avec succès !');
    }
}
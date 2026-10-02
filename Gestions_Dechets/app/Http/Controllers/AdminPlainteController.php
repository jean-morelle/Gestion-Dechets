<?php

namespace App\Http\Controllers;

use App\Models\Plainte;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Réclamations des citoyens : réponse de la mairie et suivi
 */
class AdminPlainteController extends Controller
{
    const STATUTS = ['en_attente' => 'En attente', 'en_cours' => 'En cours', 'traite' => 'Répondu', 'ferme' => 'Close'];

    public function __construct(private NotificationService $notifications)
    {
    }

    public function index(Request $request)
    {
        $statut = $request->query('statut', 'a_traiter');

        $query = Plainte::with('user');
        match ($statut) {
            'a_traiter' => $query->whereIn('statut', ['en_attente', 'en_cours']),
            'repondues' => $query->whereIn('statut', ['traite', 'ferme']),
            default => null,
        };

        $plaintes = $query
            ->orderByRaw("CASE priorite WHEN 'urgente' THEN 0 WHEN 'elevee' THEN 1 WHEN 'moyenne' THEN 2 ELSE 3 END")
            ->oldest()
            ->paginate(20)
            ->withQueryString();

        $aTraiter = Plainte::whereIn('statut', ['en_attente', 'en_cours'])->count();

        return view('admin.plaintes.index', compact('plaintes', 'statut', 'aTraiter'));
    }

    public function show(Plainte $plainte)
    {
        $plainte->load('user', 'traitePar', 'signalement');

        return view('admin.plaintes.show', compact('plainte'));
    }

    public function update(Request $request, Plainte $plainte)
    {
        $donnees = $request->validate([
            'statut' => ['required', Rule::in(array_keys(self::STATUTS))],
            'reponse' => ['nullable', 'required_if:statut,traite,ferme', 'string', 'max:2000'],
        ], [
            'reponse.required_if' => 'Rédigez la réponse envoyée au citoyen.',
        ], ['reponse' => 'réponse']);

        $plainte->update($donnees + [
            'traite_par' => $request->user()->id,
            'date_traitement' => now(),
        ]);

        if ($plainte->wasChanged(['statut', 'reponse'])) {
            $this->notifications->creerNotification(
                $plainte->user_id,
                'plainte',
                'Réponse à votre plainte',
                $plainte->reponse ?: 'Votre plainte « ' . $plainte->sujet . ' » est maintenant « ' . mb_strtolower(self::STATUTS[$plainte->statut]) . ' ».',
                route('citoyen.plaintes.show', $plainte)
            );
        }

        return redirect()->route('admin.plaintes.show', $plainte)->with('success', 'Plainte mise à jour, le citoyen est prévenu.');
    }
}

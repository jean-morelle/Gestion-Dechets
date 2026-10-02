<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Traitement des signalements (dépôts sauvages, bacs débordants…) envoyés par les citoyens
 */
class AdminSignalementController extends Controller
{
    const STATUTS = ['en_attente' => 'En attente', 'en_cours' => 'Pris en charge', 'traite' => 'Traité', 'annule' => 'Sans suite'];

    public function __construct(private NotificationService $notifications)
    {
    }

    public function index(Request $request)
    {
        $statut = $request->query('statut', 'a_traiter');

        $query = Signalement::with('user');
        match ($statut) {
            'a_traiter' => $query->whereIn('statut', ['en_attente', 'en_cours']),
            'traites' => $query->where('statut', 'traite'),
            'sans_suite' => $query->where('statut', 'annule'),
            default => null,
        };
        if ($request->filled('quartier')) {
            $query->where('quartier', $request->quartier);
        }

        $signalements = $query
            ->orderByRaw("CASE statut WHEN 'en_attente' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE priorite WHEN 'urgente' THEN 0 WHEN 'elevee' THEN 1 WHEN 'moyenne' THEN 2 ELSE 3 END")
            ->oldest()
            ->paginate(20)
            ->withQueryString();

        $aTraiter = Signalement::whereIn('statut', ['en_attente', 'en_cours'])->count();
        $quartiers = Signalement::distinct()->orderBy('quartier')->pluck('quartier');

        return view('admin.signalements.index', compact('signalements', 'statut', 'aTraiter', 'quartiers'));
    }

    public function show(Signalement $signalement)
    {
        $signalement->load('user');

        return view('admin.signalements.show', compact('signalement'));
    }

    public function update(Request $request, Signalement $signalement)
    {
        $donnees = $request->validate([
            'statut' => ['required', Rule::in(array_keys(self::STATUTS))],
            'priorite' => ['required', Rule::in(['faible', 'moyenne', 'elevee', 'urgente'])],
            'date_collecte_prevue' => ['nullable', 'date'],
            'notes_admin' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'date_collecte_prevue' => 'date d’intervention',
            'notes_admin' => 'message au citoyen',
        ]);

        if ($donnees['statut'] === 'traite' && ! $signalement->date_collecte_reelle) {
            $donnees['date_collecte_reelle'] = now();
        }

        $signalement->update($donnees);

        // Le citoyen n'est prévenu que si quelque chose le concerne a changé
        if ($signalement->wasChanged(['statut', 'date_collecte_prevue', 'notes_admin'])) {
            $message = match ($signalement->statut) {
                'en_cours' => 'Votre signalement est pris en charge' . ($signalement->date_collecte_prevue ? ', intervention prévue le ' . $signalement->date_collecte_prevue->translatedFormat('j F') : '') . '.',
                'traite' => 'Votre signalement a été traité. Merci pour votre vigilance !',
                'annule' => 'Votre signalement a été classé sans suite.',
                default => 'Votre signalement a été mis à jour.',
            };
            if ($signalement->notes_admin && $signalement->wasChanged('notes_admin')) {
                $message .= ' Message de la mairie : ' . $signalement->notes_admin;
            }

            $this->notifications->creerNotification(
                $signalement->user_id,
                'signalement',
                'Signalement : ' . mb_strtolower(self::STATUTS[$signalement->statut]),
                $message,
                route('citoyen.signalements.show', $signalement)
            );
        }

        return redirect()->route('admin.signalements.show', $signalement)->with('success', 'Signalement mis à jour.');
    }
}

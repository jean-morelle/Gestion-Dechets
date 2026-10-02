<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Incidents signalés par les collecteurs pendant les tournées
 */
class AdminIncidentController extends Controller
{
    public function __construct(private NotificationService $notifications)
    {
    }

    public function index(Request $request)
    {
        $statut = $request->query('statut', 'ouverts');

        $query = Incident::with(['collecteur', 'itineraire']);
        match ($statut) {
            'ouverts' => $query->whereIn('statut', ['signale', 'en_cours']),
            'resolus' => $query->whereIn('statut', ['resolu', 'annule']),
            default => null,
        };

        $incidents = $query
            ->orderByRaw("CASE priorite WHEN 'urgente' THEN 0 WHEN 'elevee' THEN 1 WHEN 'normale' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $ouverts = Incident::whereIn('statut', ['signale', 'en_cours'])->count();

        return view('admin.incidents.index', compact('incidents', 'statut', 'ouverts'));
    }

    public function show(Incident $incident)
    {
        $incident->load(['collecteur', 'itineraire']);

        return view('admin.incidents.show', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        $donnees = $request->validate([
            'statut' => ['required', Rule::in(['signale', 'en_cours', 'resolu', 'annule'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ], [], ['admin_notes' => 'réponse']);

        $ancienStatut = $incident->statut;
        $incident->update($donnees + [
            'date_resolution' => in_array($donnees['statut'], ['resolu', 'annule'], true) ? ($incident->date_resolution ?? now()) : null,
        ]);

        if ($ancienStatut !== $incident->statut || $incident->wasChanged('admin_notes')) {
            $this->notifications->creerNotification(
                $incident->collecteur_id,
                'itineraire',
                'Incident : ' . mb_strtolower($incident->statut_label),
                $incident->admin_notes ?: 'Votre incident « ' . $incident->type_label . ' » est maintenant « ' . mb_strtolower($incident->statut_label) . ' ».',
                route('collecteur.incidents.show', $incident)
            );
        }

        return redirect()->route('admin.incidents.show', $incident)->with('success', 'Incident mis à jour, le collecteur est prévenu.');
    }
}

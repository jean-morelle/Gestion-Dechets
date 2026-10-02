<?php

namespace App\Http\Controllers;

use App\Models\DemandeCollecte;
use App\Models\Incident;
use App\Models\PointDeCollecte;
use App\Models\Signalement;

/**
 * Vue d'ensemble de la commune : tout ce qui demande une action, sur une carte
 */
class AdminCarteController extends Controller
{
    public function index()
    {
        $signalements = Signalement::whereIn('statut', ['en_attente', 'en_cours'])
            ->whereNotNull('latitude')->whereNotNull('longitude')
            ->get(['id', 'type_dechet', 'quartier', 'latitude', 'longitude', 'priorite', 'created_at'])
            ->map(fn ($s) => [
                'calque' => 'signalements', 'etat' => 'rate',
                'lat' => (float) $s->latitude, 'lng' => (float) $s->longitude,
                'nom' => $s->type_dechet_label, 'detail' => $s->quartier . ' · ' . $s->created_at->diffForHumans(),
                'url' => route('admin.signalements.show', $s->id),
            ]);

        $demandes = DemandeCollecte::whereIn('statut', ['en_attente', 'accepte'])
            ->whereNotNull('latitude')->whereNotNull('longitude')
            ->get(['id', 'objet', 'quartier', 'latitude', 'longitude', 'statut', 'date_collecte_prevue'])
            ->map(fn ($d) => [
                'calque' => 'demandes', 'etat' => 'bleu',
                'lat' => (float) $d->latitude, 'lng' => (float) $d->longitude,
                'nom' => $d->objet,
                'detail' => $d->quartier . ' · ' . ($d->statut === 'accepte' && $d->date_collecte_prevue ? 'prévue le ' . $d->date_collecte_prevue->format('d/m') : 'à traiter'),
                'url' => route('admin.demandes.show', $d->id),
            ]);

        $incidents = Incident::whereIn('statut', ['signale', 'en_cours'])
            ->whereNotNull('latitude')->whereNotNull('longitude')
            ->get()
            ->map(fn ($i) => [
                'calque' => 'incidents', 'etat' => 'afaire',
                'lat' => (float) $i->latitude, 'lng' => (float) $i->longitude,
                'nom' => $i->type_label, 'detail' => $i->created_at->diffForHumans(),
                'url' => route('admin.incidents.show', $i->id),
            ]);

        $points = PointDeCollecte::actif()->get(['id', 'nom', 'quartier', 'latitude', 'longitude'])
            ->map(fn ($p) => [
                'calque' => 'points', 'etat' => 'fait',
                'lat' => $p->latitude, 'lng' => $p->longitude,
                'nom' => $p->nom, 'detail' => $p->quartier,
                'url' => route('admin.points.edit', $p->id),
            ]);

        $elements = $signalements->concat($demandes)->concat($incidents)->concat($points)->values();
        $compteurs = [
            'signalements' => $signalements->count(),
            'demandes' => $demandes->count(),
            'incidents' => $incidents->count(),
            'points' => $points->count(),
        ];

        return view('admin.carte', compact('elements', 'compteurs'));
    }
}

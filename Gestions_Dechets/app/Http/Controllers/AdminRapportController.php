<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use App\Models\DemandeCollecte;
use App\Models\Plainte;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Rapport d'activité sur une période (imprimable) et exports pour Excel
 */
class AdminRapportController extends Controller
{
    public function index(Request $request)
    {
        [$du, $au, $quartier] = $this->periode($request);

        $signalements = $this->filtrer(Signalement::query(), $du, $au, $quartier)->get();
        $demandes = $this->filtrer(DemandeCollecte::query(), $du, $au, $quartier)->get();
        $plaintes = $this->filtrer(Plainte::query(), $du, $au, $quartier)->get();
        $passages = $this->passages($du, $au, $quartier)->with('pointDeCollecte')->get();

        // Délai moyen entre la réception et le traitement d'un signalement
        $delais = $signalements->where('statut', 'traite')->filter(fn ($s) => $s->date_collecte_reelle)
            ->map(fn ($s) => $s->created_at->diffInHours($s->date_collecte_reelle));

        $chiffres = [
            'signalements' => $signalements->count(),
            'signalements_traites' => $signalements->where('statut', 'traite')->count(),
            'delai_moyen_heures' => $delais->isNotEmpty() ? (int) round($delais->avg()) : null,
            'demandes' => $demandes->count(),
            'demandes_effectuees' => $demandes->where('statut', 'termine')->count(),
            'demandes_refusees' => $demandes->where('statut', 'refuse')->count(),
            'plaintes' => $plaintes->count(),
            'plaintes_repondues' => $plaintes->whereIn('statut', ['traite', 'ferme'])->count(),
            'passages' => $passages->where('statut', 'termine')->count(),
            'passages_rates' => $passages->where('statut', 'rate')->count(),
            'kg' => (float) $passages->where('statut', 'termine')->sum('quantite'),
            'gps_valides' => $passages->where('statut', 'termine')->where('validation_gps', true)->count(),
        ];

        // Détail par quartier (le quartier d'un passage est celui de son point de collecte)
        $parQuartier = collect()
            ->merge($signalements->pluck('quartier'))->merge($demandes->pluck('quartier'))
            ->merge($passages->map(fn ($p) => $p->pointDeCollecte?->quartier))
            ->filter()->unique()->sort()->values()
            ->map(fn ($q) => [
                'quartier' => $q,
                'signalements' => $signalements->where('quartier', $q)->count(),
                'signalements_traites' => $signalements->where('quartier', $q)->where('statut', 'traite')->count(),
                'demandes' => $demandes->where('quartier', $q)->count(),
                'kg' => (float) $passages->filter(fn ($p) => $p->statut === 'termine' && $p->pointDeCollecte?->quartier === $q)->sum('quantite'),
            ]);

        $quartiers = Signalement::distinct()->pluck('quartier')
            ->merge(DemandeCollecte::distinct()->pluck('quartier'))
            ->filter()->unique()->sort()->values();

        return view('admin.rapports', compact('du', 'au', 'quartier', 'chiffres', 'parQuartier', 'quartiers'));
    }

    /**
     * Export CSV (séparateur « ; » et encodage reconnus par Excel en français)
     */
    public function exporter(Request $request, string $jeu): StreamedResponse
    {
        [$du, $au, $quartier] = $this->periode($request);

        [$entetes, $lignes] = match ($jeu) {
            'signalements' => [
                ['N°', 'Reçu le', 'Quartier', 'Adresse', 'Type de déchet', 'Urgence', 'Statut', 'Traité le', 'Latitude', 'Longitude'],
                $this->filtrer(Signalement::query(), $du, $au, $quartier)->oldest()->get()->map(fn ($s) => [
                    $s->id, $s->created_at->format('d/m/Y H:i'), $s->quartier, $s->adresse, $s->type_dechet_label,
                    $s->priorite_label, AdminSignalementController::STATUTS[$s->statut] ?? $s->statut,
                    $s->date_collecte_reelle?->format('d/m/Y H:i'), $s->latitude, $s->longitude,
                ]),
            ],
            'demandes' => [
                ['N°', 'Reçue le', 'Quartier', 'Adresse', 'Objet', 'Type', 'Urgence', 'Statut', 'Passage prévu', 'Raison du refus'],
                $this->filtrer(DemandeCollecte::query(), $du, $au, $quartier)->oldest()->get()->map(fn ($d) => [
                    $d->id, $d->created_at->format('d/m/Y H:i'), $d->quartier, $d->adresse, $d->objet, $d->type_collecte_label,
                    $d->urgence_label, $d->statut_label, $d->date_collecte_prevue?->format('d/m/Y'), $d->raison_refus,
                ]),
            ],
            'plaintes' => [
                ['N°', 'Reçue le', 'Quartier', 'Motif', 'Sujet', 'Priorité', 'Statut', 'Réponse', 'Répondu le'],
                $this->filtrer(Plainte::query(), $du, $au, $quartier)->oldest()->get()->map(fn ($p) => [
                    $p->id, $p->created_at->format('d/m/Y H:i'), $p->quartier, $p->type_plainte_label, $p->sujet, $p->priorite_label,
                    AdminPlainteController::STATUTS[$p->statut] ?? $p->statut, $p->reponse, $p->date_traitement?->format('d/m/Y H:i'),
                ]),
            ],
            'passages' => [
                ['Date', 'Heure', 'Tournée', 'Collecteur', 'Point', 'Quartier', 'Résultat', 'Type de déchet', 'Quantité (kg)', 'Raison', 'Écart GPS (m)'],
                $this->passages($du, $au, $quartier)->with(['pointDeCollecte', 'itineraire', 'collecteur'])->orderBy('date_collecte')->get()->map(fn ($c) => [
                    $c->date_collecte?->format('d/m/Y'), $c->heure_fin?->format('H:i'), $c->itineraire?->nom, $c->collecteur?->name,
                    $c->pointDeCollecte?->nom, $c->pointDeCollecte?->quartier, $c->statut_label, $c->type_dechet_label,
                    $c->quantite !== null ? number_format((float) $c->quantite, 2, ',', '') : '', $c->motif_echec_label, $c->distance_point,
                ]),
            ],
        };

        $nom = "collectplus-{$jeu}-{$du->format('Y-m-d')}-au-{$au->format('Y-m-d')}.csv";

        return response()->streamDownload(function () use ($entetes, $lignes) {
            $sortie = fopen('php://output', 'w');
            fwrite($sortie, "\xEF\xBB\xBF"); // BOM : accents corrects dans Excel
            fputcsv($sortie, $entetes, ';');
            foreach ($lignes as $ligne) {
                // Un texte saisi par un citoyen commençant par = + - @ serait exécuté comme formule par Excel
                $ligne = array_map(fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v, $ligne);
                fputcsv($sortie, $ligne, ';');
            }
            fclose($sortie);
        }, $nom, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Période demandée : le mois en cours par défaut */
    private function periode(Request $request): array
    {
        $request->validate([
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date', 'after_or_equal:du'],
            'quartier' => ['nullable', 'string', 'max:100'],
        ]);

        $du = $request->filled('du') ? Carbon::parse($request->du)->startOfDay() : today()->startOfMonth();
        $au = $request->filled('au') ? Carbon::parse($request->au)->endOfDay() : today()->endOfDay();

        return [$du, $au, $request->quartier ?: null];
    }

    private function filtrer($query, Carbon $du, Carbon $au, ?string $quartier)
    {
        return $query->whereBetween('created_at', [$du, $au])
            ->when($quartier, fn ($q) => $q->where('quartier', $quartier));
    }

    /** Passages effectués ou manqués pendant la période */
    private function passages(Carbon $du, Carbon $au, ?string $quartier)
    {
        return Collecte::query()
            ->whereIn('statut', [Collecte::STATUT_TERMINE, Collecte::STATUT_RATE])
            ->whereBetween('date_collecte', [$du->toDateString(), $au->toDateString()])
            ->when($quartier, fn ($q) => $q->whereHas('pointDeCollecte', fn ($p) => $p->where('quartier', $quartier)));
    }
}

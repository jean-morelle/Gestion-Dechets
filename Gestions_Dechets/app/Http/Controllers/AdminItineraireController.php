<?php

namespace App\Http\Controllers;

use App\Models\Itineraire;
use App\Models\PointDeCollecte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Tournées de collecte : composition (points dans l'ordre), affectation et suivi
 */
class AdminItineraireController extends Controller
{
    public function index(Request $request)
    {
        $query = Itineraire::with(['collecteur', 'collectes'])
            ->withCount(['pointsDeCollecte', 'incidents'])
            ->orderByRaw("CASE statut WHEN 'en_cours' THEN 0 WHEN 'planifie' THEN 1 ELSE 2 END")
            ->orderByDesc('date_debut');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('collecteur_id')) {
            $query->where('collecteur_id', $request->collecteur_id);
        }

        $itineraires = $query->paginate(15)->withQueryString();
        $collecteurs = $this->collecteurs();

        return view('admin.itineraires.index', compact('itineraires', 'collecteurs'));
    }

    public function create()
    {
        $itineraire = new Itineraire([
            'type' => Itineraire::TYPE_PONCTUEL,
            'date_debut' => today(),
            'heure_debut' => '07:00',
            'heure_fin' => '12:00',
        ]);

        return view('admin.itineraires.form', $this->donneesFormulaire($itineraire));
    }

    public function store(Request $request)
    {
        $donnees = $this->valider($request);

        $itineraire = DB::transaction(function () use ($donnees, $request) {
            $itineraire = Itineraire::create($donnees + [
                'statut' => Itineraire::STATUT_PLANIFIE,
                'admin_id' => $request->user()->id,
            ]);
            $itineraire->definirEtapes($request->input('points'));

            return $itineraire;
        });

        return redirect()->route('admin.itineraires.show', $itineraire)->with('success', 'Tournée planifiée. Le collecteur la voit dans son espace.');
    }

    public function show(Itineraire $itineraire)
    {
        $itineraire->load(['collecteur', 'pointsDeCollecte', 'collectes.pointDeCollecte', 'incidents.collecteur']);
        $collectesParPoint = $itineraire->collectes->keyBy('point_collecte_id');

        return view('admin.itineraires.show', compact('itineraire', 'collectesParPoint'));
    }

    public function edit(Itineraire $itineraire)
    {
        if (! $itineraire->peutEtreModifie()) {
            return redirect()->route('admin.itineraires.show', $itineraire)
                ->with('error', 'Une tournée démarrée ne peut plus être modifiée.');
        }

        return view('admin.itineraires.form', $this->donneesFormulaire($itineraire));
    }

    public function update(Request $request, Itineraire $itineraire)
    {
        if (! $itineraire->peutEtreModifie()) {
            return redirect()->route('admin.itineraires.show', $itineraire)
                ->with('error', 'Une tournée démarrée ne peut plus être modifiée.');
        }

        $donnees = $this->valider($request);

        DB::transaction(function () use ($itineraire, $donnees, $request) {
            $itineraire->update($donnees);
            $itineraire->definirEtapes($request->input('points'));
        });

        return redirect()->route('admin.itineraires.show', $itineraire)->with('success', 'Tournée mise à jour.');
    }

    /**
     * Une tournée planifiée peut être supprimée ; une tournée démarrée est annulée
     * (ses collectes restent dans l'historique).
     */
    public function destroy(Itineraire $itineraire)
    {
        if ($itineraire->statut === Itineraire::STATUT_PLANIFIE) {
            $itineraire->delete();

            return redirect()->route('admin.itineraires.index')->with('success', 'Tournée supprimée.');
        }

        return back()->with('error', 'Seule une tournée qui n’a pas encore démarré peut être supprimée.');
    }

    private function valider(Request $request): array
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Itineraire::TYPES))],
            'collecteur_id' => ['required', Rule::exists('users', 'id')->where('role', 'collecteur')->where('statut', 'actif')],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i', 'after:heure_debut'],
            'duree_estimee' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'description' => ['nullable', 'string', 'max:1000'],
            'points' => ['required', 'array', 'min:1', 'max:60'],
            'points.*' => ['integer', 'distinct', Rule::exists('point_de_collectes', 'id')],
        ], [
            'collecteur_id.exists' => 'Choisissez un collecteur actif.',
            'points.required' => 'Ajoutez au moins un point de collecte à la tournée.',
            'points.min' => 'Ajoutez au moins un point de collecte à la tournée.',
            'heure_fin.after' => 'L’heure de fin doit être après l’heure de début.',
        ], [
            'date_debut' => 'date',
            'heure_debut' => 'heure de début',
            'heure_fin' => 'heure de fin',
        ]);

        return $request->only(['nom', 'type', 'collecteur_id', 'date_debut', 'date_fin', 'heure_debut', 'heure_fin', 'duree_estimee', 'description']);
    }

    private function donneesFormulaire(Itineraire $itineraire): array
    {
        $selection = old('points', $itineraire->exists ? $itineraire->pointsDeCollecte->pluck('id')->all() : []);

        // Points proposables : les actifs, plus ceux déjà dans la tournée (même désactivés depuis)
        $points = PointDeCollecte::query()
            ->where(fn ($q) => $q->where('statut', PointDeCollecte::STATUT_ACTIF)->orWhereIn('id', $selection))
            ->orderBy('quartier')->orderBy('nom')
            ->get(['id', 'nom', 'quartier', 'adresse', 'latitude', 'longitude', 'statut']);

        return [
            'itineraire' => $itineraire,
            'collecteurs' => $this->collecteurs(),
            'points' => $points,
            'selection' => array_map('intval', $selection),
        ];
    }

    private function collecteurs()
    {
        return User::where('role', 'collecteur')->where('statut', 'actif')->orderBy('name')->get(['id', 'name']);
    }
}

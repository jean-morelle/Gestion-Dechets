<?php

namespace App\Http\Controllers;

use App\Models\PointDeCollecte;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Points de collecte (bacs, marchés, dépôts…) par lesquels passent les tournées
 */
class AdminPointCollecteController extends Controller
{
    public function index(Request $request)
    {
        $query = PointDeCollecte::withCount('collectes')->orderBy('quartier')->orderBy('nom');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) => $w->where('nom', 'like', "%$q%")->orWhere('adresse', 'like', "%$q%"));
        }
        if ($request->filled('quartier')) {
            $query->where('quartier', $request->quartier);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $points = $query->paginate(20)->withQueryString();
        $quartiers = PointDeCollecte::distinct()->orderBy('quartier')->pluck('quartier');

        // Tous les points (filtrés) pour la carte, pas seulement la page courante
        $pointsCarte = (clone $query)->reorder()->limit(500)->get(['id', 'nom', 'latitude', 'longitude', 'statut']);

        return view('admin.points.index', compact('points', 'quartiers', 'pointsCarte'));
    }

    public function create()
    {
        return view('admin.points.form', ['point' => new PointDeCollecte(['statut' => PointDeCollecte::STATUT_ACTIF, 'type' => PointDeCollecte::TYPE_PUBLIC])]);
    }

    public function store(Request $request)
    {
        PointDeCollecte::create($this->valider($request));

        return redirect()->route('admin.points.index')->with('success', 'Point de collecte ajouté.');
    }

    public function edit(PointDeCollecte $point)
    {
        return view('admin.points.form', compact('point'));
    }

    public function update(Request $request, PointDeCollecte $point)
    {
        $point->update($this->valider($request));

        return redirect()->route('admin.points.index')->with('success', 'Point de collecte mis à jour.');
    }

    public function destroy(PointDeCollecte $point)
    {
        // Un point déjà collecté fait partie de l'historique : on le désactive au lieu de l'effacer
        if ($point->collectes()->exists() || $point->itineraires()->exists()) {
            $point->update(['statut' => PointDeCollecte::STATUT_INACTIF]);

            return back()->with('success', 'Ce point figure dans des tournées : il a été désactivé plutôt que supprimé.');
        }

        $point->delete();

        return back()->with('success', 'Point de collecte supprimé.');
    }

    private function valider(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(PointDeCollecte::TYPES))],
            'statut' => ['required', Rule::in(array_keys(PointDeCollecte::STATUTS))],
            'adresse' => ['required', 'string', 'max:255'],
            'quartier' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'capacite' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'contact_responsable' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
        ], [
            'latitude.required' => 'Placez le point sur la carte.',
            'longitude.required' => 'Placez le point sur la carte.',
        ]);
    }
}

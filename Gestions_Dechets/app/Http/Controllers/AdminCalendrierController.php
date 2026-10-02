<?php

namespace App\Http\Controllers;

use App\Models\CalendrierCollecte;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Calendrier des passages par quartier : affiché aux habitants et utilisé
 * pour leur rappel automatique la veille (commande collectes:rappeler)
 */
class AdminCalendrierController extends Controller
{
    const TYPES = ['menagere' => 'Ordures ménagères', 'recyclage' => 'Recyclables', 'vert' => 'Déchets verts', 'encombrant' => 'Encombrants'];
    const FREQUENCES = ['hebdomadaire' => 'Chaque semaine', 'quotidienne' => 'Tous les jours', 'mensuelle' => 'Chaque mois', 'ponctuelle' => 'Une seule fois'];
    const JOURS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

    public function index(Request $request)
    {
        $query = CalendrierCollecte::query()->orderBy('quartier')->orderBy('heure_debut');

        if ($request->filled('quartier')) {
            $query->where('quartier', $request->quartier);
        }
        if ($request->query('statut') !== 'tous') {
            $query->where('statut', CalendrierCollecte::STATUT_ACTIF);
        }

        $calendriers = $query->paginate(25)->withQueryString();
        $quartiers = CalendrierCollecte::distinct()->orderBy('quartier')->pluck('quartier');

        return view('admin.calendrier.index', compact('calendriers', 'quartiers'));
    }

    public function create()
    {
        return view('admin.calendrier.form', ['calendrier' => new CalendrierCollecte([
            'frequence' => 'hebdomadaire', 'type_collecte' => 'menagere', 'statut' => 'actif',
            'heure_debut' => '06:00', 'heure_fin' => '10:00',
        ])]);
    }

    public function store(Request $request)
    {
        CalendrierCollecte::create($this->valider($request) + ['responsable_id' => $request->user()->id]);

        return redirect()->route('admin.calendrier.index')->with('success', 'Passage ajouté au calendrier. Les habitants du quartier seront prévenus la veille.');
    }

    public function edit(CalendrierCollecte $calendrier)
    {
        return view('admin.calendrier.form', compact('calendrier'));
    }

    public function update(Request $request, CalendrierCollecte $calendrier)
    {
        $calendrier->update($this->valider($request));

        return redirect()->route('admin.calendrier.index')->with('success', 'Calendrier mis à jour.');
    }

    public function destroy(CalendrierCollecte $calendrier)
    {
        $calendrier->delete();

        return redirect()->route('admin.calendrier.index')->with('success', 'Passage retiré du calendrier.');
    }

    private function valider(Request $request): array
    {
        $donnees = $request->validate([
            'type_collecte' => ['required', Rule::in(array_keys(self::TYPES))],
            'quartier' => ['required', 'string', 'max:100'],
            'frequence' => ['required', Rule::in(array_keys(self::FREQUENCES))],
            'jour_semaine' => ['nullable', 'required_if:frequence,hebdomadaire', Rule::in(self::JOURS)],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i', 'after:heure_debut'],
            'date_debut' => ['nullable', 'required_if:frequence,ponctuelle', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'statut' => ['required', Rule::in(['actif', 'suspendu'])],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'jour_semaine.required_if' => 'Choisissez le jour de passage.',
            'date_debut.required_if' => 'Indiquez la date du passage.',
            'heure_fin.after' => 'L’heure de fin doit être après l’heure de début.',
        ], [
            'date_debut' => 'date', 'heure_debut' => 'heure de début', 'heure_fin' => 'heure de fin',
        ]);

        if ($donnees['frequence'] !== 'hebdomadaire') {
            $donnees['jour_semaine'] = null;
        }
        $donnees['nom'] = self::TYPES[$donnees['type_collecte']] . ' — ' . $donnees['quartier'];

        return $donnees;
    }
}

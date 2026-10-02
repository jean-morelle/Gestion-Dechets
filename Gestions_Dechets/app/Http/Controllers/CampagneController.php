<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampagneController extends Controller
{
    /**
     * Afficher la liste des campagnes de sensibilisation
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        $query = Campagne::visible()->pourQuartier($quartier);
        
        // Filtre par type
        if ($request->filled('type')) {
            $query->parType($request->type);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        $campagnes = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Statistiques
        $stats = [
            'total' => Campagne::visible()->pourQuartier($quartier)->count(),
            'affiches' => Campagne::visible()->pourQuartier($quartier)->parType('affiche')->count(),
            'videos' => Campagne::visible()->pourQuartier($quartier)->parType('video')->count(),
            'messages' => Campagne::visible()->pourQuartier($quartier)->parType('message')->count(),
            'infographies' => Campagne::visible()->pourQuartier($quartier)->parType('infographie')->count(),
        ];
        
        return view('citoyen.campagnes.index', compact('campagnes', 'stats', 'quartier'));
    }

    /**
     * Afficher une campagne spécifique
     */
    public function show(Campagne $campagne)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        // Vérifier que la campagne est visible et cible le quartier
        if (!$campagne->estVisible() || !$campagne->cibleQuartier($quartier)) {
            abort(404);
        }
        
        // Incrémenter le nombre de vues
        $campagne->incrementerVues();
        
        return view('citoyen.campagnes.show', compact('campagne'));
    }

    /**
     * Partager une campagne
     */
    public function partager(Campagne $campagne)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        // Vérifier que la campagne est visible et cible le quartier
        if (!$campagne->estVisible() || !$campagne->cibleQuartier($quartier)) {
            abort(404);
        }
        
        // Incrémenter le nombre de partages
        $campagne->incrementerPartages();
        
        return response()->json([
            'success' => true,
            'message' => 'Campagne partagée avec succès !',
            'partages' => $campagne->fresh()->partages
        ]);
    }

    /**
     * Télécharger une ressource de campagne
     */
    public function telecharger(Campagne $campagne)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        // Vérifier que la campagne est visible et cible le quartier
        if (!$campagne->estVisible() || !$campagne->cibleQuartier($quartier)) {
            abort(404);
        }
        
        // Vérifier qu'il y a un fichier à télécharger
        if (!$campagne->fichier) {
            abort(404);
        }
        
        $filePath = storage_path('app/public/' . $campagne->fichier);
        
        if (!file_exists($filePath)) {
            abort(404);
        }
        
        return response()->download($filePath, $campagne->titre . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }
    /**
     * Afficher la liste des campagnes pour les collecteurs
     */
    public function indexCollecteur(Request $request)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        $query = Campagne::visible()->pourQuartier($quartier);
        
        // Filtre par type
        if ($request->filled('type')) {
            $query->parType($request->type);
        }
        
        $campagnes = $query->orderBy('created_at', 'desc')->paginate(8);
        
        // Statistiques
        $stats = [
            'total' => Campagne::visible()->pourQuartier($quartier)->count(),
            'affiches' => Campagne::visible()->pourQuartier($quartier)->parType('affiche')->count(),
            'videos' => Campagne::visible()->pourQuartier($quartier)->parType('video')->count(),
            'messages' => Campagne::visible()->pourQuartier($quartier)->parType('message')->count(),
            'infographies' => Campagne::visible()->pourQuartier($quartier)->parType('infographie')->count(),
        ];
        
        return view('collecteur.campagnes.index', compact('campagnes', 'stats', 'quartier'));
    }

    /**
     * Afficher une campagne spécifique pour les collecteurs
     */
    public function showCollecteur(Campagne $campagne)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        // Vérifier que la campagne est visible et cible le quartier
        if (!$campagne->estVisible() || !$campagne->cibleQuartier($quartier)) {
            abort(404);
        }
        
        return view('collecteur.campagnes.show', compact('campagne', 'quartier'));
    }

    /**
     * Partager une campagne (collecteur)
     */
    public function partagerCollecteur(Campagne $campagne)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        // Vérifier que la campagne est visible et cible le quartier
        if (!$campagne->estVisible() || !$campagne->cibleQuartier($quartier)) {
            abort(404);
        }
        
        // Incrémenter le nombre de partages
        $campagne->incrementerPartages();
        
        return response()->json([
            'success' => true,
            'message' => 'Campagne partagée avec succès !',
            'partages' => $campagne->fresh()->partages
        ]);
    }

    /**
     * Interface de partage mobile pour les collecteurs
     */
    public function partageMobile(Campagne $campagne)
    {
        $user = Auth::user();
        $quartier = $user->quartier ?? 'Centre-ville';
        
        // Vérifier que la campagne est visible et cible le quartier
        if (!$campagne->estVisible() || !$campagne->cibleQuartier($quartier)) {
            abort(404);
        }
        
        return view('collecteur.campagnes.partage-mobile', compact('campagne', 'quartier'));
    }
}
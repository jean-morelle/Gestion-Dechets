<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campagne;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCampagneController extends Controller
{
    /**
     * Afficher la liste des campagnes
     */
    public function index()
    {
        $campagnes = Campagne::orderBy('created_at', 'desc')->paginate(15);
        
        $statistiques = [
            'total' => Campagne::count(),
            'actives' => Campagne::where('statut', 'active')->count(),
            'brouillons' => Campagne::where('statut', 'brouillon')->count(),
            'terminees' => Campagne::where('statut', 'terminee')->count(),
        ];

        return view('admin.campagnes.index', compact('campagnes', 'statistiques'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.campagnes.create');
    }

    /**
     * Enregistrer une nouvelle campagne
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:affiche,video,message,infographie',
            'fichier' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'url_video' => 'nullable|url',
            'contenu_message' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'quartiers_cibles' => 'nullable|array',
            'quartiers_cibles.*' => 'string|max:255',
        ]);

        $data = $request->all();
        
        // Gestion du fichier
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filename = time() . '_' . Str::slug($request->titre) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('campagnes', $filename, 'public');
            $data['fichier'] = $path;
        }

        // Gestion de l'image de prévisualisation
        if ($request->hasFile('image_preview')) {
            $file = $request->file('image_preview');
            $filename = 'preview_' . time() . '_' . Str::slug($request->titre) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('campagnes/previews', $filename, 'public');
            $data['image_preview'] = $path;
        }

        $data['statut'] = $request->has('publier') ? 'active' : 'brouillon';

        $campagne = Campagne::create($data);

        // Notifier les collecteurs et citoyens de la nouvelle campagne
        if ($data['statut'] === 'active') {
            $this->notifierCollecteurs($campagne);
            $this->notifierCitoyens($campagne);
        }

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne créée avec succès !');
    }

    /**
     * Afficher les détails d'une campagne
     */
    public function show(Campagne $campagne)
    {
        return view('admin.campagnes.show', compact('campagne'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Campagne $campagne)
    {
        return view('admin.campagnes.edit', compact('campagne'));
    }

    /**
     * Mettre à jour une campagne
     */
    public function update(Request $request, Campagne $campagne)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:affiche,video,message,infographie',
            'fichier' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'url_video' => 'nullable|url',
            'contenu_message' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'quartiers_cibles' => 'nullable|array',
            'quartiers_cibles.*' => 'string|max:255',
        ]);

        $data = $request->all();
        
        // Gestion du fichier
        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier
            if ($campagne->fichier) {
                Storage::disk('public')->delete($campagne->fichier);
            }
            
            $file = $request->file('fichier');
            $filename = time() . '_' . Str::slug($request->titre) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('campagnes', $filename, 'public');
            $data['fichier'] = $path;
        }

        // Gestion de l'image de prévisualisation
        if ($request->hasFile('image_preview')) {
            // Supprimer l'ancienne image
            if ($campagne->image_preview) {
                Storage::disk('public')->delete($campagne->image_preview);
            }
            
            $file = $request->file('image_preview');
            $filename = 'preview_' . time() . '_' . Str::slug($request->titre) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('campagnes/previews', $filename, 'public');
            $data['image_preview'] = $path;
        }

        $data['statut'] = $request->has('publier') ? 'active' : 'brouillon';

        $campagne->update($data);

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne mise à jour avec succès !');
    }

    /**
     * Supprimer une campagne
     */
    public function destroy(Campagne $campagne)
    {
        // Supprimer les fichiers associés
        if ($campagne->fichier) {
            Storage::disk('public')->delete($campagne->fichier);
        }
        if ($campagne->image_preview) {
            Storage::disk('public')->delete($campagne->image_preview);
        }

        $campagne->delete();

        return redirect()->route('admin.campagnes.index')
            ->with('success', 'Campagne supprimée avec succès !');
    }

    /**
     * Publier une campagne
     */
    public function publier(Campagne $campagne)
    {
        $campagne->update(['statut' => 'active']);
        
        // Notifier les collecteurs
        $this->notifierCollecteurs($campagne);

        return redirect()->back()
            ->with('success', 'Campagne publiée avec succès !');
    }

    /**
     * Archiver une campagne
     */
    public function archiver(Campagne $campagne)
    {
        $campagne->update(['statut' => 'archivee']);

        return redirect()->back()
            ->with('success', 'Campagne archivée avec succès !');
    }

    /**
     * Notifier les collecteurs d'une nouvelle campagne
     */
    private function notifierCollecteurs(Campagne $campagne)
    {
        $collecteurs = User::where('role', 'collecteur')->get();

        foreach ($collecteurs as $collecteur) {
            \App\Models\Notification::create([
                'user_id' => $collecteur->id,
                'expediteur_id' => Auth::id(),
                'type' => \App\Models\Notification::TYPE_SYSTEME,
                'titre' => 'Nouvelle campagne de sensibilisation',
                'message' => "Une nouvelle campagne '{$campagne->titre}' est disponible. Vous pouvez la partager avec les citoyens lors de vos tournées.",
                'priorite' => \App\Models\Notification::PRIORITE_MOYENNE,
                'lien_action' => route('collecteur.campagnes.show', $campagne),
                'icone' => 'fas fa-bullhorn',
                'date_envoi' => now(),
                'statut' => \App\Models\Notification::STATUT_NON_LU,
                'data' => [
                    'campagne_id' => $campagne->id,
                    'type' => 'nouvelle_campagne'
                ]
            ]);
        }
    }

    /**
     * Notifier les citoyens d'une nouvelle campagne
     */
    private function notifierCitoyens(Campagne $campagne)
    {
        $citoyens = User::where('role', 'citoyen')->get();

        foreach ($citoyens as $citoyen) {
            \App\Models\Notification::create([
                'user_id' => $citoyen->id,
                'expediteur_id' => Auth::id(),
                'type' => \App\Models\Notification::TYPE_SYSTEME,
                'titre' => 'Nouvelle campagne de sensibilisation',
                'message' => "Une nouvelle campagne '{$campagne->titre}' est disponible. Découvrez-la maintenant !",
                'priorite' => \App\Models\Notification::PRIORITE_MOYENNE,
                'lien_action' => route('citoyen.campagnes.show', $campagne),
                'icone' => 'fas fa-bullhorn',
                'date_envoi' => now(),
                'statut' => \App\Models\Notification::STATUT_NON_LU,
                'data' => [
                    'campagne_id' => $campagne->id,
                    'type' => 'nouvelle_campagne_admin'
                ]
            ]);
        }
    }

    /**
     * Obtenir les statistiques d'une campagne
     */
    public function statistiques(Campagne $campagne)
    {
        $statistiques = [
            'vues' => $campagne->vues,
            'partages' => $campagne->partages,
            'collecteurs_actifs' => User::where('role', 'collecteur')->count(),
            'citoyens_cibles' => User::where('role', 'citoyen')->count(),
        ];

        return view('admin.campagnes.statistiques', compact('campagne', 'statistiques'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Comptes utilisateurs : les citoyens s'inscrivent seuls ; les collecteurs et
 * les agents de la mairie sont créés ici, avec un mot de passe provisoire.
 */
class AdminUtilisateurController extends Controller
{
    const ROLES = ['citoyen' => 'Citoyen', 'collecteur' => 'Collecteur', 'admin' => 'Administrateur'];
    const STATUTS = ['actif' => 'Actif', 'suspendu' => 'Suspendu', 'inactif' => 'Désactivé'];

    public function index(Request $request)
    {
        $query = User::query()->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) => $w->where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->orWhere('telephone', 'like', "%$q%")
                ->orWhere('quartier', 'like', "%$q%"));
        }

        $utilisateurs = $query->paginate(25)->withQueryString();
        $compteurs = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('admin.utilisateurs.index', compact('utilisateurs', 'compteurs'));
    }

    public function create(Request $request)
    {
        return view('admin.utilisateurs.create', ['role' => $request->query('role', 'collecteur')]);
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'quartier' => ['nullable', 'string', 'max:100'],
            'role' => ['required', Rule::in(['collecteur', 'admin'])],
        ], [], ['name' => 'nom']);

        $motDePasse = $this->motDePasseProvisoire();

        $utilisateur = User::create($donnees + [
            'password' => Hash::make($motDePasse),
            'statut' => 'actif',
            'doit_changer_mot_de_passe' => true,
        ]);

        return redirect()->route('admin.utilisateurs.show', $utilisateur)
            ->with('mot_de_passe_provisoire', $motDePasse)
            ->with('success', 'Compte créé.');
    }

    public function show(User $utilisateur)
    {
        $activite = match ($utilisateur->role) {
            'citoyen' => [
                'Signalements' => $utilisateur->signalements()->count(),
                'Demandes de collecte' => $utilisateur->demandesCollecte()->count(),
                'Plaintes' => $utilisateur->plaintes()->count(),
            ],
            'collecteur' => [
                'Tournées' => $utilisateur->itineraires()->count(),
                'Passages validés' => $utilisateur->collectes()->where('statut', 'termine')->count(),
                'Incidents signalés' => $utilisateur->incidents()->count(),
            ],
            default => [],
        };

        return view('admin.utilisateurs.show', [
            'utilisateur' => $utilisateur,
            'activite' => $activite,
            'estMoi' => $utilisateur->is(auth()->user()),
        ]);
    }

    public function update(Request $request, User $utilisateur)
    {
        $this->interdireSurSoiMeme($utilisateur, 'modifier votre propre rôle ou statut');

        $donnees = $request->validate([
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'statut' => ['required', Rule::in(array_keys(self::STATUTS))],
        ]);

        if ($utilisateur->role === 'admin' && ($donnees['role'] !== 'admin' || $donnees['statut'] !== 'actif')) {
            $this->garderUnAdmin($utilisateur);
        }

        $utilisateur->update($donnees);

        return redirect()->route('admin.utilisateurs.show', $utilisateur)->with('success', 'Compte mis à jour.');
    }

    /**
     * Génère un nouveau mot de passe provisoire (compte oublié, agent remplacé…)
     */
    public function reinitialiserMotDePasse(User $utilisateur)
    {
        $this->interdireSurSoiMeme($utilisateur, 'réinitialiser votre propre mot de passe ici (passez par Paramètres)');

        $motDePasse = $this->motDePasseProvisoire();
        $utilisateur->update([
            'password' => Hash::make($motDePasse),
            'mot_de_passe_defini' => true,
            'doit_changer_mot_de_passe' => true,
        ]);

        return redirect()->route('admin.utilisateurs.show', $utilisateur)
            ->with('mot_de_passe_provisoire', $motDePasse)
            ->with('success', 'Nouveau mot de passe provisoire généré.');
    }

    /**
     * Un compte qui a une activité (signalements, tournées…) est désactivé :
     * le supprimer effacerait l'historique qui lui est rattaché.
     */
    public function destroy(User $utilisateur)
    {
        $this->interdireSurSoiMeme($utilisateur, 'supprimer votre propre compte');
        if ($utilisateur->role === 'admin') {
            $this->garderUnAdmin($utilisateur);
        }

        $aDeLActivite = $utilisateur->signalements()->exists()
            || $utilisateur->demandesCollecte()->exists()
            || $utilisateur->plaintes()->exists()
            || $utilisateur->itineraires()->exists()
            || $utilisateur->collectes()->exists();

        if ($aDeLActivite) {
            $utilisateur->update(['statut' => 'inactif']);

            return redirect()->route('admin.utilisateurs.show', $utilisateur)
                ->with('success', 'Ce compte a un historique : il a été désactivé plutôt que supprimé.');
        }

        $utilisateur->delete();

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Compte supprimé.');
    }

    /** 10 caractères faciles à dicter : sans 0/O ni 1/l/I */
    private function motDePasseProvisoire(): string
    {
        $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        return collect(range(1, 10))->map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)])->implode('');
    }

    private function interdireSurSoiMeme(User $utilisateur, string $action): void
    {
        if ($utilisateur->is(auth()->user())) {
            abort(redirect()->route('admin.utilisateurs.show', $utilisateur)->with('error', "Vous ne pouvez pas $action."));
        }
    }

    private function garderUnAdmin(User $utilisateur): void
    {
        $autresAdmins = User::where('role', 'admin')->where('statut', 'actif')->whereKeyNot($utilisateur->id)->count();
        if ($autresAdmins === 0) {
            abort(redirect()->route('admin.utilisateurs.show', $utilisateur)->with('error', 'Il doit rester au moins un administrateur actif.'));
        }
    }
}

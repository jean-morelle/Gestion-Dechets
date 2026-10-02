<?php

namespace App\Http\Controllers;

use App\Models\DemandeCollecte;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Traitement des demandes de collecte des citoyens :
 * en attente → acceptée (date de passage) → terminée, ou refusée (avec raison).
 */
class AdminDemandeController extends Controller
{
    public function __construct(private NotificationService $notifications)
    {
    }

    public function index(Request $request)
    {
        // Par défaut : ce qui reste à traiter
        $statut = $request->query('statut', 'a_traiter');

        $query = DemandeCollecte::with('user', 'collecteur');
        match ($statut) {
            'a_traiter' => $query->where('statut', DemandeCollecte::STATUT_EN_ATTENTE),
            'planifiees' => $query->whereIn('statut', [DemandeCollecte::STATUT_ACCEPTE, DemandeCollecte::STATUT_EN_COURS]),
            'terminees' => $query->where('statut', DemandeCollecte::STATUT_TERMINE),
            'refusees' => $query->where('statut', DemandeCollecte::STATUT_REFUSE),
            default => null,
        };

        if ($request->filled('quartier')) {
            $query->where('quartier', $request->quartier);
        }

        // Les plus urgentes et les plus anciennes d'abord
        $demandes = $query
            ->orderByRaw("CASE urgence WHEN 'urgente' THEN 0 WHEN 'elevee' THEN 1 WHEN 'moyenne' THEN 2 ELSE 3 END")
            ->oldest()
            ->paginate(20)
            ->withQueryString();

        $compteurs = [
            'a_traiter' => DemandeCollecte::where('statut', DemandeCollecte::STATUT_EN_ATTENTE)->count(),
            'planifiees' => DemandeCollecte::whereIn('statut', [DemandeCollecte::STATUT_ACCEPTE, DemandeCollecte::STATUT_EN_COURS])->count(),
        ];
        $quartiers = DemandeCollecte::distinct()->orderBy('quartier')->pluck('quartier');

        return view('admin.demandes.index', compact('demandes', 'statut', 'compteurs', 'quartiers'));
    }

    public function show(DemandeCollecte $demande)
    {
        $demande->load('user', 'admin', 'collecteur');
        $collecteurs = User::where('role', 'collecteur')->where('statut', 'actif')->orderBy('name')->get(['id', 'name']);

        return view('admin.demandes.show', compact('demande', 'collecteurs'));
    }

    public function accepter(Request $request, DemandeCollecte $demande)
    {
        $this->verifierStatut($demande, [DemandeCollecte::STATUT_EN_ATTENTE, DemandeCollecte::STATUT_ACCEPTE]);

        $donnees = $request->validate([
            'date_collecte_prevue' => ['required', 'date', 'after_or_equal:today'],
            'collecteur_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'collecteur')],
        ], [], ['date_collecte_prevue' => 'date de passage', 'collecteur_id' => 'collecteur']);

        $nouvelle = $demande->statut === DemandeCollecte::STATUT_EN_ATTENTE;
        $demande->update($donnees + [
            'statut' => DemandeCollecte::STATUT_ACCEPTE,
            'admin_id' => $request->user()->id,
            'date_traitement' => now(),
        ]);

        $date = $demande->date_collecte_prevue->translatedFormat('l j F');
        $this->notifierCitoyen(
            $demande,
            $nouvelle ? 'Demande de collecte acceptée' : 'Date de passage modifiée',
            "Votre demande « {$demande->objet} » est planifiée : passage prévu le {$date}."
        );

        return redirect()->route('admin.demandes.show', $demande)->with('success', 'Demande planifiée, le citoyen est prévenu.');
    }

    public function refuser(Request $request, DemandeCollecte $demande)
    {
        $this->verifierStatut($demande, [DemandeCollecte::STATUT_EN_ATTENTE, DemandeCollecte::STATUT_ACCEPTE]);

        $donnees = $request->validate([
            'raison_refus' => ['required', 'string', 'max:1000'],
        ], [], ['raison_refus' => 'raison']);

        $demande->update($donnees + [
            'statut' => DemandeCollecte::STATUT_REFUSE,
            'admin_id' => $request->user()->id,
            'date_traitement' => now(),
        ]);

        $this->notifierCitoyen($demande, 'Demande de collecte refusée', "Votre demande « {$demande->objet} » n’a pas pu être acceptée : {$demande->raison_refus}");

        return redirect()->route('admin.demandes.show', $demande)->with('success', 'Demande refusée, le citoyen est prévenu.');
    }

    public function terminer(Request $request, DemandeCollecte $demande)
    {
        $this->verifierStatut($demande, [DemandeCollecte::STATUT_ACCEPTE, DemandeCollecte::STATUT_EN_COURS]);

        $demande->update(['statut' => DemandeCollecte::STATUT_TERMINE]);

        $this->notifierCitoyen($demande, 'Collecte effectuée', "La collecte de votre demande « {$demande->objet} » a été effectuée. Merci !");

        return redirect()->route('admin.demandes.show', $demande)->with('success', 'Demande marquée comme effectuée.');
    }

    private function verifierStatut(DemandeCollecte $demande, array $autorises): void
    {
        if (! in_array($demande->statut, $autorises, true)) {
            abort(redirect()->route('admin.demandes.show', $demande)
                ->with('error', 'Cette action n’est plus possible : la demande est « ' . $demande->statut_label . ' ».'));
        }
    }

    private function notifierCitoyen(DemandeCollecte $demande, string $titre, string $message): void
    {
        $this->notifications->creerNotification(
            $demande->user_id,
            'collecte',
            $titre,
            $message,
            route('citoyen.demandes-collecte.show', $demande)
        );
    }
}

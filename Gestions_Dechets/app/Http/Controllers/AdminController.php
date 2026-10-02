<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Signalement;
use App\Models\Plainte;
use App\Models\Itineraire;
use App\Models\CalendrierCollecte;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tableau de bord administrateur
     */
    public function dashboard()
    {
        $adminId = auth()->id();
        
        $statistiques = [
            'utilisateurs' => [
                'total' => User::count(),
                'citoyens' => User::where('role', 'citoyen')->count(),
                'collecteurs' => User::where('role', 'collecteur')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
            'signalements' => [
                'total' => Signalement::count(),
                'en_attente' => Signalement::where('statut', 'en_attente')->count(),
                'traites' => Signalement::where('statut', 'traite')->count(),
            ],
            'plaintes' => [
                'total' => Plainte::count(),
                'en_attente' => Plainte::where('statut', 'en_attente')->count(),
                'traitees' => Plainte::where('statut', 'traite')->count(),
            ],
            'itineraires' => [
                'total' => Itineraire::count(),
                'actifs' => Itineraire::where('statut', 'en_cours')->count(),
                'termines' => Itineraire::where('statut', 'termine')->count(),
            ],
            'messages' => [
                'total' => Message::where('receiver_id', $adminId)->count(),
                'non_lus' => Message::where('receiver_id', $adminId)->where('is_read', false)->count(),
                'recus' => Message::where('receiver_id', $adminId)->count(),
                'envoyes' => Message::where('sender_id', $adminId)->count(),
            ]
        ];

        // Ce qui attend une action de l'administration
        $signalementsRecents = Signalement::where('statut', Signalement::STATUT_EN_ATTENTE)->latest()->limit(5)->get();
        $plaintesRecentes = Plainte::where('statut', Plainte::STATUT_EN_ATTENTE)->latest()->limit(5)->get();

        // Signalements reçus par jour sur les 30 derniers jours
        $debut = now()->subDays(29)->startOfDay();
        $parJour = Signalement::where('created_at', '>=', $debut)
            ->selectRaw('DATE(created_at) as jour, COUNT(*) as total')
            ->groupBy('jour')
            ->pluck('total', 'jour');

        $evolution = ['labels' => [], 'valeurs' => []];
        for ($date = $debut->copy(); $date->lte(now()); $date->addDay()) {
            $evolution['labels'][] = $date->translatedFormat('d M');
            $evolution['valeurs'][] = (int) ($parJour[$date->toDateString()] ?? 0);
        }

        $parType = Signalement::selectRaw('type_dechet, COUNT(*) as total')
            ->groupBy('type_dechet')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($s) => ['label' => $s->type_dechet_label, 'total' => (int) $s->total]);

        return view('admin.dashboard', compact('statistiques', 'signalementsRecents', 'plaintesRecentes', 'evolution', 'parType'));
    }

}


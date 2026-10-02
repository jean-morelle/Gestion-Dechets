<?php

namespace Tests\Feature;

use App\Models\CalendrierCollecte;
use App\Models\Campagne;
use App\Models\DemandeCollecte;
use App\Models\Incident;
use App\Models\Itineraire;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Plainte;
use App\Models\PointDeCollecte;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Ouvre toutes les pages (GET) de chaque espace avec un jeu de données complet
 * et vérifie qu'aucune ne renvoie d'erreur serveur.
 */
class ExplorationTest extends TestCase
{
    use RefreshDatabase;

    public function test_aucune_page_ne_plante(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'quartier' => 'Bè']);
        $collecteur = User::factory()->create(['role' => 'collecteur', 'quartier' => 'Bè']);
        $citoyen = User::factory()->create(['role' => 'citoyen', 'quartier' => 'Bè', 'telephone' => '+228 90 00 00 00', 'adresse' => 'Rue 1']);

        $point = PointDeCollecte::create(['nom' => 'Bac', 'type' => 'public', 'adresse' => 'Rue', 'quartier' => 'Bè', 'latitude' => 6.13, 'longitude' => 1.22, 'statut' => 'actif']);
        $itineraire = Itineraire::create(['nom' => 'Tournée', 'type' => 'ponctuel', 'collecteur_id' => $collecteur->id, 'admin_id' => $admin->id,
            'date_debut' => today(), 'heure_debut' => '07:00', 'heure_fin' => '10:00', 'statut' => 'planifie']);
        $itineraire->definirEtapes([$point->id]);
        $itineraire->demarrer();
        $collecte = $itineraire->collectes()->first();
        $incident = Incident::create(['collecteur_id' => $collecteur->id, 'itineraire_id' => $itineraire->id, 'type_incident' => 'autre', 'description' => 'x', 'statut' => 'signale', 'priorite' => 'normale']);
        $signalement = Signalement::create(['user_id' => $citoyen->id, 'type_dechet' => 'encombrant', 'description' => 'x', 'adresse' => 'x', 'quartier' => 'Bè', 'latitude' => 6.1, 'longitude' => 1.2]);
        $plainte = Plainte::create(['user_id' => $citoyen->id, 'type_plainte' => 'autre', 'sujet' => 's', 'description' => 'd', 'adresse' => 'a', 'quartier' => 'Bè', 'priorite' => 'moyenne', 'statut' => 'en_attente']);
        $demande = DemandeCollecte::create(['user_id' => $citoyen->id, 'type_collecte' => 'encombrant', 'objet' => 'o', 'description' => 'd', 'adresse' => 'a', 'quartier' => 'Bè', 'urgence' => 'moyenne', 'statut' => 'en_attente']);
        $campagne = Campagne::create(['titre' => 'Tri', 'description' => 'd', 'type' => 'message', 'contenu_message' => 'Triez', 'statut' => 'active']);
        $calendrier = CalendrierCollecte::create(['nom' => 'Bè hebdo', 'type_collecte' => 'menagere', 'quartier' => 'Bè', 'frequence' => 'hebdomadaire', 'jour_semaine' => 'lundi', 'heure_debut' => '07:00', 'heure_fin' => '09:00', 'statut' => 'actif']);
        $message = Message::create(['sender_id' => $admin->id, 'receiver_id' => $citoyen->id, 'subject' => 'Bonjour', 'message' => 'Test']);
        $notifs = collect([$admin, $collecteur, $citoyen])->mapWithKeys(fn ($u) => [$u->id => Notification::create([
            'user_id' => $u->id, 'type' => 'systeme', 'titre' => 'T', 'message' => 'M', 'statut' => 'non_lu', 'priorite' => 'moyenne', 'date_envoi' => now(),
        ])]);

        $erreurs = [];
        foreach (['admin' => $admin, 'collecteur' => $collecteur, 'citoyen' => $citoyen] as $role => $user) {
            $valeurs = [
                'itineraire' => $itineraire->id, 'point' => $point->id, 'collecte' => $collecte->id, 'incident' => $incident->id,
                'signalement' => $signalement->id, 'plainte' => $plainte->id, 'demande' => $demande->id, 'demandeCollecte' => $demande->id,
                'campagne' => $campagne->id, 'calendrier' => $calendrier->id, 'message' => $message->id, 'utilisateur' => $citoyen->id,
                'user' => $citoyen->id, 'notification' => $notifs[$user->id]->id, 'notificationId' => $notifs[$user->id]->id,
                'type' => 'message', 'token' => 'x',
            ];

            foreach (Route::getRoutes() as $route) {
                if (! in_array('GET', $route->methods()) || str_starts_with($route->uri(), '_') || str_contains($route->uri(), 'auth/google')
                    || str_starts_with($route->uri(), 'two-factor') || $route->uri() === 'up' || str_starts_with($route->uri(), 'storage')) {
                    continue;
                }
                $uri = preg_replace_callback('/\{(\w+)\??\}/', fn ($m) => $valeurs[$m[1]] ?? 1, $route->uri());

                $statut = $this->actingAs($user)->get('/' . ltrim($uri, '/'))->getStatusCode();
                if ($statut >= 500) {
                    $erreurs[] = "$role  GET /$uri  → $statut";
                }
            }
        }

        $this->assertSame([], $erreurs, "Pages en erreur :\n" . implode("\n", $erreurs));
    }
}

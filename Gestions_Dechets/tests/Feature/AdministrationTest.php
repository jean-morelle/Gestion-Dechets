<?php

namespace Tests\Feature;

use App\Models\DemandeCollecte;
use App\Models\Incident;
use App\Models\Itineraire;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Traitement des demandes de collecte, des incidents et gestion des comptes
 */
class AdministrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function demande(User $citoyen): DemandeCollecte
    {
        return DemandeCollecte::create([
            'user_id' => $citoyen->id, 'type_collecte' => 'encombrant', 'objet' => 'Vieux matelas',
            'description' => 'Un matelas et une armoire', 'adresse' => 'Rue 12', 'quartier' => 'Tokoin',
            'urgence' => 'moyenne', 'statut' => 'en_attente',
        ]);
    }

    public function test_toutes_les_pages_du_menu_admin_s_affichent(): void
    {
        foreach (['/admin/dashboard', '/admin/carte', '/admin/rapports', '/admin/signalements', '/admin/demandes', '/admin/plaintes',
                  '/admin/itineraires', '/admin/points', '/admin/incidents', '/admin/calendrier', '/admin/utilisateurs',
                  '/admin/campagnes', '/admin/notifications'] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    // ---------- Demandes de collecte ----------

    public function test_l_admin_planifie_une_demande_et_le_citoyen_est_prevenu(): void
    {
        $citoyen = User::factory()->create();
        $collecteur = User::factory()->create(['role' => 'collecteur']);
        $demande = $this->demande($citoyen);

        $this->actingAs($this->admin)->get('/admin/demandes')->assertOk()->assertSee('Vieux matelas');
        $this->actingAs($this->admin)->get("/admin/demandes/{$demande->id}")->assertOk();

        $this->actingAs($this->admin)->post("/admin/demandes/{$demande->id}/accepter", [
            'date_collecte_prevue' => today()->addDays(2)->toDateString(),
            'collecteur_id' => $collecteur->id,
        ])->assertSessionHasNoErrors();

        $demande->refresh();
        $this->assertSame('accepte', $demande->statut);
        $this->assertSame($collecteur->id, $demande->collecteur_id);
        $this->assertTrue(Notification::where('user_id', $citoyen->id)->where('titre', 'Demande de collecte acceptée')->exists());

        // Le citoyen voit la date de passage
        $this->actingAs($citoyen)->get("/citoyen/demandes-collecte/{$demande->id}")->assertOk()->assertSee('Passage prévu');

        $this->actingAs($this->admin)->post("/admin/demandes/{$demande->id}/terminer")->assertSessionHasNoErrors();
        $this->assertSame('termine', $demande->fresh()->statut);
    }

    public function test_un_refus_exige_une_raison_transmise_au_citoyen(): void
    {
        $citoyen = User::factory()->create();
        $demande = $this->demande($citoyen);

        $this->actingAs($this->admin)->post("/admin/demandes/{$demande->id}/refuser", [])->assertSessionHasErrors('raison_refus');

        $this->actingAs($this->admin)->post("/admin/demandes/{$demande->id}/refuser", [
            'raison_refus' => 'Les déchets de chantier ne sont pas pris en charge.',
        ]);
        $this->assertSame('refuse', $demande->fresh()->statut);
        $this->actingAs($citoyen)->get("/citoyen/demandes-collecte/{$demande->id}")->assertSee('déchets de chantier');

        // Une demande refusée ne peut plus être terminée
        $this->actingAs($this->admin)->post("/admin/demandes/{$demande->id}/terminer");
        $this->assertSame('refuse', $demande->fresh()->statut);
    }

    // ---------- Incidents ----------

    public function test_l_admin_traite_un_incident_et_repond_au_collecteur(): void
    {
        $collecteur = User::factory()->create(['role' => 'collecteur']);
        $tournee = Itineraire::create([
            'nom' => 'Tournée', 'type' => 'ponctuel', 'collecteur_id' => $collecteur->id, 'admin_id' => $this->admin->id,
            'date_debut' => today(), 'heure_debut' => '07:00', 'heure_fin' => '10:00', 'statut' => 'en_cours',
        ]);
        $incident = Incident::create([
            'collecteur_id' => $collecteur->id, 'itineraire_id' => $tournee->id, 'type_incident' => 'panne_vehicule',
            'description' => 'Pneu crevé', 'statut' => 'signale', 'priorite' => 'elevee',
        ]);

        $this->actingAs($this->admin)->get('/admin/incidents')->assertOk()->assertSee('Pneu crevé');
        $this->actingAs($this->admin)->get("/admin/incidents/{$incident->id}")->assertOk();

        $this->actingAs($this->admin)->put("/admin/incidents/{$incident->id}", [
            'statut' => 'resolu', 'admin_notes' => 'Dépanneuse envoyée',
        ])->assertSessionHasNoErrors();

        $incident->refresh();
        $this->assertSame('resolu', $incident->statut);
        $this->assertNotNull($incident->date_resolution);
        $this->assertTrue(Notification::where('user_id', $collecteur->id)->exists());
        $this->actingAs($collecteur)->get("/collecteur/incidents/{$incident->id}")->assertSee('Dépanneuse envoyée');
    }

    // ---------- Comptes ----------

    public function test_creation_d_un_collecteur_avec_mot_de_passe_provisoire(): void
    {
        $this->actingAs($this->admin)->get('/admin/utilisateurs/create')->assertOk();

        $reponse = $this->actingAs($this->admin)->post('/admin/utilisateurs', [
            'name' => 'Kossi Agbeko', 'email' => 'kossi@mairie.tg', 'telephone' => '+228 90 00 00 00', 'role' => 'collecteur',
        ])->assertSessionHasNoErrors();

        $agent = User::where('email', 'kossi@mairie.tg')->firstOrFail();
        $motDePasse = session('mot_de_passe_provisoire');
        $this->assertSame('collecteur', $agent->role);
        $this->assertTrue($agent->doit_changer_mot_de_passe);
        $this->assertSame(10, strlen($motDePasse));
        $this->assertTrue(Hash::check($motDePasse, $agent->password));
        $reponse->assertRedirect("/admin/utilisateurs/{$agent->id}");

        // Première connexion : l'agent est renvoyé vers le changement de mot de passe
        auth()->logout();
        $this->post('/login', ['email' => 'kossi@mairie.tg', 'password' => $motDePasse]);
        $this->get('/collecteur/dashboard')->assertRedirect(route('settings.index') . '#securite');

        $this->put('/settings/mot-de-passe', [
            'current_password' => $motDePasse,
            'password' => 'MonMotDePasse2026',
            'password_confirmation' => 'MonMotDePasse2026',
        ])->assertSessionHasNoErrors();

        $this->assertFalse($agent->fresh()->doit_changer_mot_de_passe);
        $this->get('/collecteur/dashboard')->assertOk();
    }

    public function test_un_citoyen_ne_peut_pas_creer_de_compte_agent(): void
    {
        $citoyen = User::factory()->create();

        $this->actingAs($citoyen)->post('/admin/utilisateurs', [
            'name' => 'Pirate', 'email' => 'pirate@test.tg', 'role' => 'admin',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'pirate@test.tg']);
    }

    public function test_l_admin_ne_peut_pas_se_retirer_ses_propres_droits(): void
    {
        $this->actingAs($this->admin)->put("/admin/utilisateurs/{$this->admin->id}", ['role' => 'citoyen', 'statut' => 'actif']);
        $this->assertSame('admin', $this->admin->fresh()->role);

        $this->actingAs($this->admin)->delete("/admin/utilisateurs/{$this->admin->id}");
        $this->assertNotNull($this->admin->fresh());
    }

    public function test_un_compte_avec_historique_est_desactive_et_non_supprime(): void
    {
        $citoyen = User::factory()->create();
        $this->demande($citoyen);

        $this->actingAs($this->admin)->delete("/admin/utilisateurs/{$citoyen->id}");
        $this->assertSame('inactif', $citoyen->fresh()->statut);

        $vide = User::factory()->create();
        $this->actingAs($this->admin)->delete("/admin/utilisateurs/{$vide->id}")->assertRedirect('/admin/utilisateurs');
        $this->assertNull($vide->fresh());
    }

    public function test_reinitialisation_du_mot_de_passe_par_l_admin(): void
    {
        $collecteur = User::factory()->create(['role' => 'collecteur']);

        $this->actingAs($this->admin)->post("/admin/utilisateurs/{$collecteur->id}/mot-de-passe")->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check(session('mot_de_passe_provisoire'), $collecteur->fresh()->password));
        $this->assertTrue($collecteur->fresh()->doit_changer_mot_de_passe);
        $this->actingAs($this->admin)->get('/admin/utilisateurs')->assertOk()->assertSee($collecteur->name);
    }
}

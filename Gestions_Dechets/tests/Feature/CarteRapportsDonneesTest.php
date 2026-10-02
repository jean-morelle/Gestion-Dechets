<?php

namespace Tests\Feature;

use App\Models\DemandeCollecte;
use App\Models\Notification;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarteRapportsDonneesTest extends TestCase
{
    use RefreshDatabase;

    private function signalement(User $u, array $attributs = []): Signalement
    {
        return Signalement::create($attributs + [
            'user_id' => $u->id, 'type_dechet' => 'encombrant', 'description' => 'Matelas', 'adresse' => 'Rue 4',
            'quartier' => 'Bè', 'latitude' => 6.13, 'longitude' => 1.24,
        ]);
    }

    public function test_la_carte_affiche_les_signalements_a_traiter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->signalement(User::factory()->create());

        $this->actingAs($admin)->get('/admin/carte')->assertOk()->assertSee('Signalements à traiter')->assertSee('"calque":"signalements"', false);
    }

    public function test_rapport_et_export_excel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $citoyen = User::factory()->create();
        $this->signalement($citoyen, ['statut' => 'traite', 'date_collecte_reelle' => now()->addHours(5)]);
        $this->signalement($citoyen, ['adresse' => '=HYPERLINK("http://pirate")']);

        $this->actingAs($admin)->get('/admin/rapports')->assertOk()->assertSee('50 %')->assertSee('5 h');

        $csv = $this->actingAs($admin)->get('/admin/rapports/export/signalements')->assertOk()->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('"Reçu le";Quartier', $csv);
        // Formule neutralisée
        $this->assertStringContainsString("'=HYPERLINK", $csv);

        foreach (['demandes', 'plaintes', 'passages'] as $jeu) {
            $this->actingAs($admin)->get("/admin/rapports/export/{$jeu}")->assertOk();
        }
        $this->actingAs($citoyen)->get('/admin/rapports/export/signalements')->assertForbidden();
    }

    public function test_pages_legales_publiques(): void
    {
        $this->get('/confidentialite')->assertOk()->assertSee('2019-014')->assertSee('[à compléter]');
        $this->get('/conditions-utilisation')->assertOk();

        config(['collectplus.exploitant' => 'Mairie de Lomé']);
        $this->get('/confidentialite')->assertSee('Mairie de Lomé');
    }

    public function test_le_citoyen_telecharge_ses_donnees(): void
    {
        $citoyen = User::factory()->create(['name' => 'Afi Mensah']);
        $this->signalement($citoyen);

        $json = json_decode($this->actingAs($citoyen)->get('/settings/mes-donnees')->assertOk()->streamedContent(), true);

        $this->assertSame('Afi Mensah', $json['compte']['name']);
        $this->assertCount(1, $json['signalements']);
    }

    public function test_suppression_du_compte_anonymise_sans_perdre_les_signalements(): void
    {
        $citoyen = User::factory()->create(['name' => 'Afi Mensah', 'telephone' => '90000000']);
        $s = $this->signalement($citoyen);
        DemandeCollecte::create(['user_id' => $citoyen->id, 'type_collecte' => 'vert', 'objet' => 'o', 'description' => 'd',
            'adresse' => 'a', 'quartier' => 'Bè', 'urgence' => 'faible', 'statut' => 'en_attente', 'contact_telephone' => '90000000']);
        Notification::create(['user_id' => $citoyen->id, 'type' => 'systeme', 'titre' => 't', 'message' => 'm', 'statut' => 'non_lu', 'priorite' => 'moyenne', 'date_envoi' => now()]);

        $this->actingAs($citoyen)->delete('/settings/compte', ['password_suppression' => 'mauvais'])->assertSessionHasErrors('password_suppression');

        $this->actingAs($citoyen)->delete('/settings/compte', ['password_suppression' => 'password'])->assertRedirect('/login');

        $citoyen->refresh();
        $this->assertSame('Ancien utilisateur', $citoyen->name);
        $this->assertNull($citoyen->telephone);
        $this->assertSame('inactif', $citoyen->statut);
        $this->assertGuest();
        $this->assertNotNull($s->fresh());
        $this->assertNull(DemandeCollecte::first()->contact_telephone);
        $this->assertSame(0, Notification::where('user_id', $citoyen->id)->count());

        // Ne peut plus se connecter avec son ancien e-mail
        $this->post('/login', ['email' => 'afi@test.tg', 'password' => 'password'])->assertSessionHasErrors();
    }

    public function test_un_agent_ne_peut_pas_supprimer_son_compte_lui_meme(): void
    {
        $collecteur = User::factory()->create(['role' => 'collecteur']);

        $this->actingAs($collecteur)->delete('/settings/compte', ['password_suppression' => 'password'])->assertForbidden();
        $this->assertSame('actif', $collecteur->fresh()->statut);
    }
}

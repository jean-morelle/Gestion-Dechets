<?php

namespace Tests\Feature;

use App\Models\Collecte;
use App\Models\Incident;
use App\Models\Itineraire;
use App\Models\Notification;
use App\Models\PointDeCollecte;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Parcours complet : l'admin compose une tournée, le collecteur la réalise,
 * l'admin suit le résultat.
 */
class ParcoursCollecteTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $collecteur;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->collecteur = User::factory()->create(['role' => 'collecteur']);
    }

    /** Image PNG 1×1 réelle (l'extension GD n'est pas nécessaire) */
    private function photo(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('passage.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
        ));
    }

    private function point(string $nom, float $lat, float $lng): PointDeCollecte
    {
        return PointDeCollecte::create([
            'nom' => $nom, 'type' => 'public', 'adresse' => 'Rue test', 'quartier' => 'Bè',
            'latitude' => $lat, 'longitude' => $lng, 'statut' => 'actif',
        ]);
    }

    private function tournee(array $points): Itineraire
    {
        $this->actingAs($this->admin)->post('/admin/itineraires', [
            'nom' => 'Tournée de Bè',
            'type' => 'ponctuel',
            'collecteur_id' => $this->collecteur->id,
            'date_debut' => today()->toDateString(),
            'heure_debut' => '07:00',
            'heure_fin' => '11:00',
            'points' => array_map(fn ($p) => $p->id, $points),
        ])->assertSessionHasNoErrors();

        return Itineraire::latest('id')->firstOrFail();
    }

    public function test_l_admin_ajoute_un_point_de_collecte(): void
    {
        $this->actingAs($this->admin)->get('/admin/points/create')->assertOk();

        $this->actingAs($this->admin)->post('/admin/points', [
            'nom' => 'Bac du marché', 'type' => 'commercial', 'statut' => 'actif',
            'adresse' => 'Rue du marché', 'quartier' => 'Bè',
            'latitude' => 6.14, 'longitude' => 1.24,
        ])->assertRedirect('/admin/points');

        $this->actingAs($this->admin)->get('/admin/points')->assertOk()->assertSee('Bac du marché');
    }

    public function test_un_point_sans_position_est_refuse(): void
    {
        $this->actingAs($this->admin)->post('/admin/points', [
            'nom' => 'Bac', 'type' => 'public', 'statut' => 'actif', 'adresse' => 'Rue', 'quartier' => 'Bè',
        ])->assertSessionHasErrors('latitude');
    }

    public function test_l_admin_compose_une_tournee_dans_l_ordre(): void
    {
        $a = $this->point('Marché', 6.1300, 1.2200);
        $b = $this->point('Gare', 6.1400, 1.2300);

        $tournee = $this->tournee([$b, $a]);

        $this->assertSame([$b->id, $a->id], $tournee->pointsDeCollecte->pluck('id')->all());
        $this->assertGreaterThan(1, (float) $tournee->distance_estimee); // ~1,6 km
        $this->actingAs($this->admin)->get("/admin/itineraires/{$tournee->id}")->assertOk()->assertSee('Gare');
        $this->actingAs($this->admin)->get("/admin/itineraires/{$tournee->id}/edit")->assertOk();
    }

    public function test_une_tournee_sans_etape_est_refusee(): void
    {
        $this->actingAs($this->admin)->post('/admin/itineraires', [
            'nom' => 'Vide', 'type' => 'ponctuel', 'collecteur_id' => $this->collecteur->id,
            'date_debut' => today()->toDateString(), 'heure_debut' => '07:00', 'heure_fin' => '11:00',
        ])->assertSessionHasErrors('points');
    }

    public function test_parcours_complet_du_collecteur(): void
    {
        $a = $this->point('Marché', 6.1300, 1.2200);
        $b = $this->point('Gare', 6.1400, 1.2300);
        $tournee = $this->tournee([$a, $b]);
        $c = $this->actingAs($this->collecteur);

        $c->get('/collecteur/itineraires')->assertOk()->assertSee('Tournée de Bè');
        $c->get("/collecteur/itineraires/{$tournee->id}")->assertOk()->assertSee('Démarrer la tournée');

        // Démarrage : une collecte « à faire » par étape
        $c->post("/collecteur/itineraires/{$tournee->id}/demarrer")->assertRedirect();
        $this->assertSame('en_cours', $tournee->fresh()->statut);
        $collectes = $tournee->collectes()->get()->keyBy('point_collecte_id');
        $this->assertCount(2, $collectes);
        $this->assertSame('prevue', $collectes[$a->id]->statut);

        // L'admin ne peut plus modifier une tournée démarrée
        $this->actingAs($this->admin)->get("/admin/itineraires/{$tournee->id}/edit")->assertRedirect("/admin/itineraires/{$tournee->id}");

        // Étape 1 : passage validé sur place (à ~20 m du point)
        $this->actingAs($this->collecteur)->post("/collecteur/collectes/{$collectes[$a->id]->id}/passage", [
            'photo' => $this->photo(),
            'type_dechet' => 'dechet_menager',
            'quantite' => 120,
            'latitude' => 6.13015,
            'longitude' => 1.22010,
            'precision' => 12,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $passage = $collectes[$a->id]->fresh();
        $this->assertSame('termine', $passage->statut);
        $this->assertTrue($passage->validation_gps);
        $this->assertLessThan(50, $passage->distance_point);
        Storage::disk('public')->assertExists($passage->photo_validation);

        // Pas de double validation
        $this->actingAs($this->collecteur)->post("/collecteur/collectes/{$passage->id}/passage", [
            'photo' => $this->photo(), 'type_dechet' => 'dechet_menager', 'quantite' => 1,
        ])->assertRedirect("/collecteur/itineraires/{$tournee->id}");
        $this->assertEquals(120, (float) $passage->fresh()->quantite);

        // Étape 2 : non collectée
        $this->actingAs($this->collecteur)->post("/collecteur/collectes/{$collectes[$b->id]->id}/echec", [
            'motif_echec' => 'acces_impossible',
        ])->assertSessionHasNoErrors();
        $this->assertSame('rate', $collectes[$b->id]->fresh()->statut);

        // Incident pendant la tournée
        $this->actingAs($this->collecteur)->get("/collecteur/incidents/create?itineraire_id={$tournee->id}")->assertOk();
        $this->actingAs($this->collecteur)->post('/collecteur/incidents', [
            'itineraire_id' => $tournee->id,
            'type_incident' => 'probleme_acces',
            'description' => 'Rue de la gare inondée',
        ])->assertRedirect("/collecteur/itineraires/{$tournee->id}");
        $incident = Incident::firstOrFail();
        $this->actingAs($this->collecteur)->get("/collecteur/incidents/{$incident->id}")->assertOk()->assertSee('Rue de la gare inondée');

        // Clôture
        $this->actingAs($this->collecteur)->post("/collecteur/itineraires/{$tournee->id}/terminer")->assertRedirect();
        $this->assertSame('termine', $tournee->fresh()->statut);

        // Pages du collecteur après la tournée
        $this->actingAs($this->collecteur)->get('/collecteur/dashboard')->assertOk();
        $this->actingAs($this->collecteur)->get('/collecteur/collectes')->assertOk()->assertSee('Marché');
        $this->actingAs($this->collecteur)->get("/collecteur/collectes/{$passage->id}")->assertOk()->assertSee('120 kg');
        $this->actingAs($this->collecteur)->get('/collecteur/incidents')->assertOk();

        // Suivi côté administration
        $this->actingAs($this->admin)->get("/admin/itineraires/{$tournee->id}")
            ->assertOk()
            ->assertSee('Rue de la gare inondée')
            ->assertSee('Accès impossible');
        $this->actingAs($this->admin)->get('/admin/itineraires')->assertOk()->assertSee('1/2 collectés');
        $this->assertGreaterThanOrEqual(3, Notification::where('user_id', $this->admin->id)->count());
    }

    public function test_un_passage_loin_du_point_est_signale(): void
    {
        $a = $this->point('Marché', 6.1300, 1.2200);
        $tournee = $this->tournee([$a]);
        $this->actingAs($this->collecteur)->post("/collecteur/itineraires/{$tournee->id}/demarrer");
        $collecte = $tournee->collectes()->firstOrFail();

        $this->actingAs($this->collecteur)->post("/collecteur/collectes/{$collecte->id}/passage", [
            'photo' => $this->photo(), 'type_dechet' => 'encombrant', 'quantite' => 40,
            'latitude' => 6.1400, 'longitude' => 1.2300, // ~1,5 km plus loin
        ])->assertSessionHasNoErrors();

        $collecte->refresh();
        $this->assertFalse($collecte->validation_gps);
        $this->assertTrue($collecte->ecart_gps_suspect);
    }

    public function test_un_collecteur_ne_voit_pas_la_tournee_d_un_autre(): void
    {
        $tournee = $this->tournee([$this->point('Marché', 6.13, 1.22)]);
        $autre = User::factory()->create(['role' => 'collecteur']);

        $this->actingAs($autre)->get("/collecteur/itineraires/{$tournee->id}")->assertForbidden();
        $this->actingAs($autre)->post("/collecteur/itineraires/{$tournee->id}/demarrer")->assertForbidden();
    }

    public function test_un_point_deja_utilise_est_desactive_et_non_supprime(): void
    {
        $a = $this->point('Marché', 6.13, 1.22);
        $this->tournee([$a]);

        $this->actingAs($this->admin)->delete("/admin/points/{$a->id}");

        $this->assertSame('inactif', $a->fresh()->statut);
    }
}

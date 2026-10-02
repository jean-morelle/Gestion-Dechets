<?php

namespace Tests\Feature;

use App\Models\CalendrierCollecte;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendrierRappelsTest extends TestCase
{
    use RefreshDatabase;

    private function passageHebdo(string $jour, string $quartier = 'Bè'): CalendrierCollecte
    {
        return CalendrierCollecte::create([
            'nom' => 'Ordures — ' . $quartier, 'type_collecte' => 'menagere', 'quartier' => $quartier,
            'frequence' => 'hebdomadaire', 'jour_semaine' => $jour, 'heure_debut' => '06:00', 'heure_fin' => '09:00', 'statut' => 'actif',
        ]);
    }

    public function test_un_passage_hebdomadaire_tombe_le_bon_jour(): void
    {
        $c = $this->passageHebdo('mercredi');

        $this->assertTrue($c->aLieuLe('2026-10-07'));   // mercredi
        $this->assertFalse($c->aLieuLe('2026-10-08'));  // jeudi

        $c->update(['statut' => 'suspendu']);
        $this->assertFalse($c->fresh()->aLieuLe('2026-10-07'));
    }

    public function test_les_habitants_du_quartier_sont_prevenus_la_veille_une_seule_fois(): void
    {
        $demain = today()->addDay();
        $jour = array_search($demain->dayOfWeekIso, CalendrierCollecte::JOURS_ISO);
        $this->passageHebdo($jour, 'Bè');

        $habitant = User::factory()->create(['quartier' => 'bè']);          // casse différente
        $desabonne = User::factory()->create(['quartier' => 'Bè', 'notification_preferences' => ['rappel_collecte' => false]]);
        $ailleurs = User::factory()->create(['quartier' => 'Tokoin']);

        $this->artisan('collectes:rappeler')->assertSuccessful();
        $this->artisan('collectes:rappeler')->assertSuccessful(); // relancée : pas de doublon

        $this->assertSame(1, Notification::where('user_id', $habitant->id)->where('type', 'calendrier')->count());
        $this->assertSame(0, Notification::where('user_id', $desabonne->id)->count());
        $this->assertSame(0, Notification::where('user_id', $ailleurs->id)->count());
        $this->assertStringStartsWith('Demain : collecte des ordures ménagères', Notification::where('user_id', $habitant->id)->value('titre'));
    }

    public function test_le_citoyen_peut_desactiver_les_rappels(): void
    {
        $citoyen = User::factory()->create(['quartier' => 'Bè']);

        $this->actingAs($citoyen)->get('/settings')->assertSee('Me prévenir la veille');
        $this->actingAs($citoyen)->put('/settings/notifications', [])->assertRedirect('/settings');

        $this->assertFalse($citoyen->fresh()->notification_preferences['rappel_collecte']);
    }

    public function test_l_admin_gere_le_calendrier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/calendrier/create')->assertOk();
        $this->actingAs($admin)->post('/admin/calendrier', [
            'quartier' => 'Bè', 'type_collecte' => 'menagere', 'frequence' => 'hebdomadaire',
            'heure_debut' => '06:00', 'heure_fin' => '09:00', 'statut' => 'actif',
        ])->assertSessionHasErrors('jour_semaine');

        $this->actingAs($admin)->post('/admin/calendrier', [
            'quartier' => 'Bè', 'type_collecte' => 'menagere', 'frequence' => 'hebdomadaire', 'jour_semaine' => 'lundi',
            'heure_debut' => '06:00', 'heure_fin' => '09:00', 'statut' => 'actif',
        ])->assertSessionHasNoErrors();

        $c = CalendrierCollecte::firstOrFail();
        $this->actingAs($admin)->get('/admin/calendrier')->assertOk()->assertSee('Chaque lundi');
        $this->actingAs($admin)->put("/admin/calendrier/{$c->id}", [
            'quartier' => 'Bè', 'type_collecte' => 'menagere', 'frequence' => 'hebdomadaire', 'jour_semaine' => 'jeudi',
            'heure_debut' => '06:00', 'heure_fin' => '09:00', 'statut' => 'suspendu',
        ])->assertSessionHasNoErrors();
        $this->assertSame('suspendu', $c->fresh()->statut);

        $this->actingAs($admin)->delete("/admin/calendrier/{$c->id}");
        $this->assertNull($c->fresh());
    }

    public function test_l_admin_voit_ses_alertes_recues(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $n = Notification::create([
            'user_id' => $admin->id, 'type' => 'signalement', 'titre' => 'Nouveau signalement', 'message' => 'Tas d’ordures',
            'statut' => 'non_lu', 'priorite' => 'moyenne', 'date_envoi' => now(), 'lien_action' => '/admin/signalements',
        ]);

        $this->actingAs($admin)->get('/admin/notifications')->assertOk()->assertSee('Nouveau signalement');
        $this->actingAs($admin)->get("/admin/notifications/{$n->id}/ouvrir")->assertRedirect('/admin/signalements');
        $this->assertSame('lu', $n->fresh()->statut);
    }
}

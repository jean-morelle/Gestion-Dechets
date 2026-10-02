<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Plainte;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Tests\TestCase;

class SecuriteTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Connexion ----------

    public function test_le_compte_est_bloque_apres_cinq_essais_rates(): void
    {
        User::factory()->create(['email' => 'ama@test.tg']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'ama@test.tg', 'password' => 'mauvais']);
        }

        // Même le bon mot de passe est refusé pendant le blocage
        $this->post('/login', ['email' => 'ama@test.tg', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertStringContainsString('Trop de tentatives', session('errors')->first('email'));
    }

    public function test_un_compte_suspendu_ne_peut_pas_se_connecter(): void
    {
        User::factory()->create(['email' => 'kofi@test.tg', 'statut' => 'suspendu']);

        $this->post('/login', ['email' => 'kofi@test.tg', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_connexion_normale(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    // ---------- Mot de passe oublié ----------

    public function test_mot_de_passe_oublie_de_bout_en_bout(): void
    {
        NotificationFacade::fake();
        $user = User::factory()->create(['email' => 'esi@test.tg']);

        $this->get('/mot-de-passe-oublie')->assertOk();
        $this->post('/mot-de-passe-oublie', ['email' => 'esi@test.tg'])->assertSessionHas('success');

        $token = null;
        NotificationFacade::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token, $user) {
            $token = $notification->token;
            // L'e-mail est en français
            return str_contains($notification->toMail($user)->subject, 'Réinitialisation');
        });

        $this->get("/reinitialiser-mot-de-passe/{$token}?email=esi@test.tg")->assertOk();
        $this->post('/reinitialiser-mot-de-passe', [
            'token' => $token, 'email' => 'esi@test.tg',
            'password' => 'NouveauMdp2026', 'password_confirmation' => 'NouveauMdp2026',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('NouveauMdp2026', $user->fresh()->password));

        // Le lien ne sert qu'une fois
        $this->post('/reinitialiser-mot-de-passe', [
            'token' => $token, 'email' => 'esi@test.tg',
            'password' => 'EncoreUn2026', 'password_confirmation' => 'EncoreUn2026',
        ])->assertSessionHasErrors('email');
    }

    public function test_mot_de_passe_oublie_ne_revele_pas_si_le_compte_existe(): void
    {
        $inconnu = $this->post('/mot-de-passe-oublie', ['email' => 'personne@test.tg']);
        $inconnu->assertSessionHas('success');
        $inconnu->assertSessionHasNoErrors();
    }

    // ---------- Formulaires citoyens ----------

    public function test_un_citoyen_cree_un_signalement_sans_pouvoir_forcer_son_traitement(): void
    {
        $citoyen = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($citoyen)->post('/citoyen/signalements', [
            'type_dechet' => 'dechet_menager', 'priorite' => 'elevee', 'description' => 'Tas d’ordures devant l’école',
            'adresse' => 'Rue 12', 'quartier' => 'Bè', 'latitude' => 6.13, 'longitude' => 1.24,
            // Champs réservés à l'administration, envoyés malicieusement
            'statut' => 'traite', 'notes_admin' => 'Déjà réglé', 'collecteur_id' => $admin->id,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $s = Signalement::firstOrFail();
        $this->assertSame('en_attente', $s->statut);
        $this->assertSame('elevee', $s->priorite);
        $this->assertNull($s->notes_admin);
        $this->assertNull($s->collecteur_id);
        // L'administration est prévenue
        $this->assertTrue(Notification::where('user_id', $admin->id)->where('type', 'signalement')->exists());
    }

    public function test_une_plainte_ne_peut_pas_contenir_la_reponse_de_la_mairie(): void
    {
        $citoyen = User::factory()->create();

        $this->actingAs($citoyen)->post('/citoyen/plaintes', [
            'type_plainte' => 'collecte_oubliee', 'sujet' => 'Bac non vidé', 'description' => 'Depuis une semaine',
            'adresse' => 'Rue 3', 'quartier' => 'Tokoin', 'priorite' => 'moyenne',
            'statut' => 'ferme', 'reponse' => 'Faux message de la mairie',
        ])->assertSessionHasNoErrors();

        $p = Plainte::firstOrFail();
        $this->assertSame('en_attente', $p->statut);
        $this->assertNull($p->reponse);
    }

    public function test_une_plainte_ne_peut_pas_viser_le_signalement_d_un_autre(): void
    {
        $autre = User::factory()->create();
        $signalementAutre = Signalement::create([
            'user_id' => $autre->id, 'type_dechet' => 'encombrant', 'description' => 'x', 'adresse' => 'x', 'quartier' => 'x',
            'latitude' => 6.1, 'longitude' => 1.2,
        ]);

        $this->actingAs(User::factory()->create())->post('/citoyen/plaintes', [
            'type_plainte' => 'autre', 'sujet' => 's', 'description' => 'd', 'adresse' => 'a', 'quartier' => 'q',
            'priorite' => 'faible', 'signalement_id' => $signalementAutre->id,
        ])->assertSessionHasErrors('signalement_id');
    }

    // ---------- Traitement par l'administration ----------

    public function test_l_admin_traite_un_signalement_et_une_plainte(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $citoyen = User::factory()->create();
        $s = Signalement::create([
            'user_id' => $citoyen->id, 'type_dechet' => 'encombrant', 'description' => 'Matelas', 'adresse' => 'Rue', 'quartier' => 'Bè',
            'latitude' => 6.1, 'longitude' => 1.2,
        ]);
        $p = Plainte::create([
            'user_id' => $citoyen->id, 'type_plainte' => 'collecte_retard', 'sujet' => 'Retard', 'description' => 'd',
            'adresse' => 'a', 'quartier' => 'Bè', 'priorite' => 'moyenne', 'statut' => 'en_attente',
        ]);

        $this->actingAs($admin)->get('/admin/signalements')->assertOk()->assertSee('Matelas');
        $this->actingAs($admin)->put("/admin/signalements/{$s->id}", [
            'statut' => 'en_cours', 'priorite' => 'elevee', 'date_collecte_prevue' => today()->addDay()->toDateString(),
            'notes_admin' => 'Une équipe passe demain',
        ])->assertSessionHasNoErrors();
        $this->assertSame('en_cours', $s->fresh()->statut);
        $this->actingAs($citoyen)->get("/citoyen/signalements/{$s->id}")->assertSee('Une équipe passe demain');

        $this->actingAs($admin)->put("/admin/plaintes/{$p->id}", ['statut' => 'traite'])->assertSessionHasErrors('reponse');
        $this->actingAs($admin)->put("/admin/plaintes/{$p->id}", ['statut' => 'traite', 'reponse' => 'Le camion est réparé.'])
            ->assertSessionHasNoErrors();
        $this->actingAs($citoyen)->get("/citoyen/plaintes/{$p->id}")->assertSee('Le camion est réparé.');

        $this->assertSame(2, Notification::where('user_id', $citoyen->id)->count());
    }

    public function test_les_entetes_de_securite_sont_presents(): void
    {
        $this->get('/login')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}

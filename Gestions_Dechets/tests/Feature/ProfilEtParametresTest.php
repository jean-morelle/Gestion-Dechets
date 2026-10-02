<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfilEtParametresTest extends TestCase
{
    use RefreshDatabase;

    public static function roles(): array
    {
        return [['citoyen'], ['collecteur'], ['admin']];
    }

    #[DataProvider('roles')]
    public function test_les_pages_s_affichent_pour_chaque_role(string $role): void
    {
        $user = User::factory()->create(['role' => $role, 'quartier' => 'Tokoin']);

        $this->actingAs($user)->get('/profil')->assertOk()->assertSee($user->name);
        $this->actingAs($user)->get('/settings')->assertOk()->assertSee('Sécurité');
    }

    public function test_le_profil_signale_les_informations_manquantes(): void
    {
        $user = User::factory()->create(['role' => 'citoyen', 'telephone' => null]);

        $this->actingAs($user)->get('/profil')->assertSee('votre numéro de téléphone');
    }

    public function test_l_apparence_est_enregistree_sur_le_compte(): void
    {
        $user = User::factory()->create(['role' => 'citoyen']);

        $this->actingAs($user)->put('/settings/appearance', [
            'theme' => 'dark', 'couleur_accent' => 'violet', 'taille_texte' => 'grande',
        ])->assertRedirect('/settings');

        $user->refresh();
        $this->assertSame(['dark', 'violet', 'grande'], [$user->theme, $user->couleur_accent, $user->taille_texte]);
        $this->actingAs($user)->get('/profil')
            ->assertSee('data-bs-theme="dark"', false)
            ->assertSee('data-accent="violet"', false)
            ->assertSee('data-taille="grande"', false);
    }

    public function test_une_couleur_inconnue_est_refusee(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/settings/appearance', [
            'theme' => 'light', 'couleur_accent' => 'rose-fluo', 'taille_texte' => 'normale',
        ])->assertSessionHasErrors('couleur_accent');
    }

    public function test_le_bouton_clair_sombre_de_la_barre_du_haut(): void
    {
        $user = User::factory()->create(['theme' => 'light']);

        // Appel en arrière-plan depuis la page (JavaScript)
        $this->actingAs($user)->putJson('/settings/theme', ['theme' => 'dark'])->assertNoContent();
        $this->assertSame('dark', $user->fresh()->theme);

        // Sans JavaScript : retour à la page d'origine
        $this->actingAs($user)->from('/citoyen/dashboard')->put('/settings/theme', ['theme' => 'light'])->assertRedirect('/citoyen/dashboard');
        $this->assertSame('light', $user->fresh()->theme);
    }

    public function test_changer_le_mot_de_passe_exige_l_ancien(): void
    {
        $user = User::factory()->create(['role' => 'citoyen']);

        $this->actingAs($user)->put('/settings/mot-de-passe', [
            'current_password' => 'mauvais',
            'password' => 'NouveauMdp2026',
            'password_confirmation' => 'NouveauMdp2026',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put('/settings/mot-de-passe', [
            'current_password' => 'password',
            'password' => 'NouveauMdp2026',
            'password_confirmation' => 'NouveauMdp2026',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('NouveauMdp2026', $user->fresh()->password));
    }

    public function test_un_compte_google_definit_son_mot_de_passe_une_seule_fois_sans_l_ancien(): void
    {
        $user = User::factory()->create(['role' => 'citoyen', 'google_id' => '123', 'mot_de_passe_defini' => false]);

        $this->actingAs($user)->get('/settings')->assertSee('Définir un mot de passe');

        $this->actingAs($user)->put('/settings/mot-de-passe', [
            'password' => 'NouveauMdp2026',
            'password_confirmation' => 'NouveauMdp2026',
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertTrue(Hash::check('NouveauMdp2026', $user->password));
        $this->assertTrue($user->mot_de_passe_defini);

        // Une fois choisi, le mot de passe actuel est exigé comme pour tout le monde
        $this->actingAs($user)->put('/settings/mot-de-passe', [
            'password' => 'AutreMdp2026',
            'password_confirmation' => 'AutreMdp2026',
        ])->assertSessionHasErrors('current_password');
    }
}

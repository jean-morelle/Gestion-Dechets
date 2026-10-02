<?php

namespace Database\Seeders;

use App\Models\Itineraire;
use App\Models\PointDeCollecte;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Données de démonstration : quelques points de collecte et une tournée
 * planifiée pour le collecteur de test. Les collectes sont créées par
 * l'application quand le collecteur démarre la tournée.
 */
class CollecteSeeder extends Seeder
{
    public function run(): void
    {
        $collecteur = User::where('role', 'collecteur')->first();
        $admin = User::where('role', 'admin')->first();

        if (! $collecteur || ! $admin) {
            $this->command->error('Il faut un collecteur et un administrateur (AdminUserSeeder).');

            return;
        }

        $points = collect([
            ['nom' => 'Bac du Grand Marché', 'adresse' => 'Rue du Grand Marché', 'quartier' => 'Centre-ville', 'latitude' => 6.1307, 'longitude' => 1.2229, 'type' => 'commercial'],
            ['nom' => 'Bac de la place de l’Indépendance', 'adresse' => 'Boulevard du 13 Janvier', 'quartier' => 'Centre-ville', 'latitude' => 6.1336, 'longitude' => 1.2205, 'type' => 'public'],
            ['nom' => 'Dépôt de Nyékonakpoè', 'adresse' => 'Rue de Nyékonakpoè', 'quartier' => 'Nyékonakpoè', 'latitude' => 6.1335, 'longitude' => 1.2085, 'type' => 'public'],
            ['nom' => 'Bac de Kodjoviakopé', 'adresse' => 'Rue de Kodjoviakopé', 'quartier' => 'Kodjoviakopé', 'latitude' => 6.1300, 'longitude' => 1.2000, 'type' => 'public'],
        ])->map(fn ($p) => PointDeCollecte::firstOrCreate(['nom' => $p['nom']], $p + ['statut' => PointDeCollecte::STATUT_ACTIF]));

        $tournee = Itineraire::firstOrCreate(
            ['nom' => 'Centre-ville – tournée du matin', 'collecteur_id' => $collecteur->id],
            [
                'admin_id' => $admin->id,
                'type' => Itineraire::TYPE_QUOTIDIEN,
                'date_debut' => today(),
                'heure_debut' => '07:00',
                'heure_fin' => '11:00',
                'description' => 'Passer au Grand Marché avant l’ouverture des étals.',
                'statut' => Itineraire::STATUT_PLANIFIE,
            ]
        );

        if ($tournee->wasRecentlyCreated) {
            $tournee->definirEtapes($points->pluck('id')->all());
        }

        $this->command->info('Tournée de démonstration prête pour ' . $collecteur->name . '.');
    }
}

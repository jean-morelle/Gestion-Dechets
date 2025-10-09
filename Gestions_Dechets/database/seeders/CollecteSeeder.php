<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Collecte;
use App\Models\User;
use App\Models\PointDeCollecte;
use App\Models\Itineraire;

class CollecteSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Récupérer un collecteur
        $collecteur = User::where('role', 'collecteur')->first();
        
        if (!$collecteur) {
            $this->command->error('Aucun collecteur trouvé. Créez d\'abord un utilisateur avec le rôle collecteur.');
            return;
        }

        // Récupérer un administrateur
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->error('Aucun administrateur trouvé. Créez d\'abord un utilisateur avec le rôle admin.');
            return;
        }

        // Créer quelques points de collecte
        $pointsDeCollecte = [
            [
                'nom' => 'Point de collecte Centre-ville',
                'adresse' => '123 Avenue de la République, Lomé',
                'quartier' => 'Centre-ville',
                'latitude' => 6.1378,
                'longitude' => 1.2123,
                'type' => 'public',
                'statut' => 'actif'
            ],
            [
                'nom' => 'Point de collecte Adidogomé',
                'adresse' => '456 Rue du Marché, Adidogomé',
                'quartier' => 'Adidogomé',
                'latitude' => 6.1456,
                'longitude' => 1.2234,
                'type' => 'public',
                'statut' => 'actif'
            ],
            [
                'nom' => 'Point de collecte Bè',
                'adresse' => '789 Boulevard de la Paix, Bè',
                'quartier' => 'Bè',
                'latitude' => 6.1234,
                'longitude' => 1.2345,
                'type' => 'public',
                'statut' => 'actif'
            ]
        ];

        foreach ($pointsDeCollecte as $pointData) {
            $point = PointDeCollecte::firstOrCreate(
                ['adresse' => $pointData['adresse']],
                $pointData
            );
        }

        // Créer un itinéraire
        $itineraire = Itineraire::firstOrCreate(
            [
                'nom' => 'Tournée matinale Centre-ville',
                'collecteur_id' => $collecteur->id
            ],
            [
                'nom' => 'Tournée matinale Centre-ville',
                'collecteur_id' => $collecteur->id,
                'admin_id' => $admin->id,
                'type' => 'quotidien',
                'date_debut' => now()->format('Y-m-d'),
                'date_fin' => now()->addDays(7)->format('Y-m-d'),
                'heure_debut' => '08:00',
                'heure_fin' => '12:00',
                'description' => 'Collecte quotidienne dans le centre-ville',
                'statut' => 'planifie'
            ]
        );

        // Récupérer les points de collecte créés
        $points = PointDeCollecte::all();
        
        if ($points->isEmpty()) {
            $this->command->error('Aucun point de collecte créé.');
            return;
        }

        // Créer des collectes
        $collectes = [
            [
                'collecteur_id' => $collecteur->id,
                'point_collecte_id' => $points[0]->id,
                'itineraire_id' => $itineraire->id,
                'type_dechet' => 'dechet_menager',
                'quantite' => 150.5,
                'statut' => 'prevue',
                'date_collecte' => now()->addDay()->format('Y-m-d'),
                'notes' => 'Collecte normale des déchets ménagers'
            ],
            [
                'collecteur_id' => $collecteur->id,
                'point_collecte_id' => $points[1]->id ?? $points[0]->id,
                'itineraire_id' => $itineraire->id,
                'type_dechet' => 'encombrant',
                'quantite' => 75.0,
                'statut' => 'en_cours',
                'date_collecte' => now()->format('Y-m-d'),
                'notes' => 'Meubles et électroménager à collecter'
            ],
            [
                'collecteur_id' => $collecteur->id,
                'point_collecte_id' => $points[2]->id ?? $points[0]->id,
                'itineraire_id' => $itineraire->id,
                'type_dechet' => 'dechet_vert',
                'quantite' => 200.0,
                'statut' => 'termine',
                'date_collecte' => now()->subDay()->format('Y-m-d'),
                'notes' => 'Collecte de déchets verts terminée avec succès'
            ],
            [
                'collecteur_id' => $collecteur->id,
                'point_collecte_id' => $points[0]->id,
                'itineraire_id' => $itineraire->id,
                'type_dechet' => 'dechet_recyclable',
                'quantite' => 100.0,
                'statut' => 'prevue',
                'date_collecte' => now()->addDays(2)->format('Y-m-d'),
                'notes' => 'Collecte des recyclables programmée'
            ],
            [
                'collecteur_id' => $collecteur->id,
                'point_collecte_id' => $points[1]->id ?? $points[0]->id,
                'itineraire_id' => $itineraire->id,
                'type_dechet' => 'dechet_dangereux',
                'quantite' => 25.0,
                'statut' => 'en_cours',
                'date_collecte' => now()->format('Y-m-d'),
                'notes' => 'ATTENTION: Déchets dangereux - nécessite précautions'
            ]
        ];

        foreach ($collectes as $collecteData) {
            Collecte::create($collecteData);
        }

        $this->command->info('Collectes créées avec succès pour le collecteur: ' . $collecteur->name);
    }
}
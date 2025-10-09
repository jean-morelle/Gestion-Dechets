<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DemandeCollecte;
use App\Models\User;

class DemandeCollecteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs citoyens
        $citoyens = User::where('role', 'citoyen')->get();
        
        if ($citoyens->isEmpty()) {
            $this->command->warn('Aucun citoyen trouvé. Créez d\'abord des utilisateurs citoyens.');
            return;
        }

        $typesCollecte = [
            'Ménagère',
            'Encombrant',
            'Vert',
            'Recyclage',
            'Dangereux'
        ];

        $priorites = [
            'Faible',
            'Moyenne',
            'Élevée',
            'Urgente'
        ];

        $statuts = [
            'En attente',
            'En cours',
            'Terminée',
            'Annulée'
        ];

        $quartiers = [
            'Centre-ville',
            'Nyékonakpoè',
            'Tokoin',
            'Bè',
            'Adidogomé',
            'Agoè',
            'Attiegou',
            'Quartier Nord',
            'Quartier Sud',
            'Quartier Est'
        ];

        $objets = [
            'Demande de collecte de déchets ménagers',
            'Collecte d\'encombrants urgente',
            'Ramassage de déchets verts',
            'Collecte de déchets recyclables',
            'Élimination de déchets dangereux',
            'Collecte exceptionnelle demandée',
            'Nouvelle demande de service',
            'Collecte programmée',
            'Demande de collecte spéciale',
            'Service de collecte personnalisé'
        ];

        $descriptions = [
            'Je souhaite programmer une collecte de déchets ménagers pour la semaine prochaine.',
            'Il y a des encombrants dans ma cour qui nécessitent une collecte urgente.',
            'J\'ai des déchets verts de jardinage à faire collecter.',
            'Je voudrais participer au programme de recyclage de ma commune.',
            'J\'ai des déchets dangereux (batteries, peintures) à faire éliminer.',
            'Nous organisons un événement et avons besoin d\'une collecte exceptionnelle.',
            'Nouvelle demande de service de collecte pour notre immeuble.',
            'Collecte programmée pour le nettoyage de notre quartier.',
            'Demande de collecte spéciale pour des déchets volumineux.',
            'Service de collecte personnalisé pour notre entreprise.'
        ];

        // Créer 30 demandes de collecte de test
        for ($i = 0; $i < 30; $i++) {
            $citoyen = $citoyens->random();
            $typeCollecte = $typesCollecte[array_rand($typesCollecte)];
            $priorite = $priorites[array_rand($priorites)];
            $statut = $statuts[array_rand($statuts)];
            $objet = $objets[array_rand($objets)];
            $description = $descriptions[array_rand($descriptions)];
            $quartier = $quartiers[array_rand($quartiers)];

            $demande = new DemandeCollecte();
            $demande->user_id = $citoyen->id;
            $demande->objet = $objet;
            $demande->type_collecte = $typeCollecte;
            $demande->priorite = $priorite;
            $demande->description = $description;
            $demande->adresse = $citoyen->adresse;
            $demande->quartier = $quartier;
            $demande->latitude = $citoyen->latitude ?? 6.1378 + (rand(-100, 100) / 1000);
            $demande->longitude = $citoyen->longitude ?? 1.2123 + (rand(-100, 100) / 1000);
            $demande->contact_telephone = $citoyen->telephone;
            $demande->statut = $statut;

            // Définir les dates selon le statut
            if ($statut === 'Terminée') {
                $demande->date_collecte_prevue = now()->subDays(rand(1, 30));
                $demande->date_collecte_effectuee = $demande->date_collecte_prevue;
            } elseif ($statut === 'En cours') {
                $demande->date_collecte_prevue = now()->addDays(rand(1, 7));
            } else {
                $demande->date_collecte_prevue = now()->addDays(rand(1, 14));
            }

            $demande->created_at = now()->subDays(rand(0, 60));
            $demande->updated_at = $demande->created_at;
            $demande->save();
        }

        $this->command->info('30 demandes de collecte de test créées avec succès !');
    }
}
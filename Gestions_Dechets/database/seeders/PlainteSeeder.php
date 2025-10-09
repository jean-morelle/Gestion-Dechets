<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plainte;
use App\Models\User;
use App\Models\Signalement;

class PlainteSeeder extends Seeder
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

        // Récupérer quelques signalements pour lier aux plaintes
        $signalements = Signalement::take(5)->get();

        $typesPlaintes = [
            Plainte::TYPE_COLLECTE_RETARD,
            Plainte::TYPE_COLLECTE_OUBLIEE,
            Plainte::TYPE_SERVICE_CLIENT,
            Plainte::TYPE_AUTRE
        ];

        $priorites = [
            Plainte::PRIORITE_FAIBLE,
            Plainte::PRIORITE_MOYENNE,
            Plainte::PRIORITE_ELEVEE,
            Plainte::PRIORITE_URGENTE
        ];

        $statuts = [
            Plainte::STATUT_EN_ATTENTE,
            Plainte::STATUT_EN_COURS,
            Plainte::STATUT_TRAITEE,
            Plainte::STATUT_FERMEE
        ];

        $objets = [
            'Retard dans la collecte des déchets',
            'Collecte oubliée dans mon quartier',
            'Service client insatisfaisant',
            'Problème avec la facturation',
            'Demande de nouveau point de collecte',
            'Signalement non traité',
            'Communication insuffisante',
            'Qualité du service dégradée',
            'Horaires de collecte inadaptés',
            'Manque d\'information'
        ];

        $descriptions = [
            'La collecte des déchets est régulièrement en retard dans mon quartier.',
            'Ma poubelle n\'a pas été vidée malgré le signalement.',
            'Le service client ne répond pas à mes demandes.',
            'Il y a des erreurs dans la facturation des services.',
            'Nous avons besoin d\'un nouveau point de collecte dans notre secteur.',
            'Mon signalement n\'a pas été traité dans les délais.',
            'Les informations sur les collectes sont insuffisantes.',
            'La qualité du service s\'est dégradée récemment.',
            'Les horaires de collecte ne conviennent pas aux habitants.',
            'Il manque des informations sur les types de déchets acceptés.'
        ];

        // Créer 20 plaintes de test
        for ($i = 0; $i < 20; $i++) {
            $citoyen = $citoyens->random();
            $typePlainte = $typesPlaintes[array_rand($typesPlaintes)];
            $priorite = $priorites[array_rand($priorites)];
            $statut = $statuts[array_rand($statuts)];
            $objet = $objets[array_rand($objets)];
            $description = $descriptions[array_rand($descriptions)];

            $plainte = new Plainte();
            $plainte->user_id = $citoyen->id;
            $plainte->type_plainte = $typePlainte;
            $plainte->objet = $objet;
            $plainte->description = $description;
            $plainte->priorite = $priorite;
            $plainte->statut = $statut;
            $plainte->adresse = $citoyen->adresse;
            $plainte->quartier = $citoyen->quartier;
            $plainte->latitude = $citoyen->latitude ?? 6.1378 + (rand(-100, 100) / 1000);
            $plainte->longitude = $citoyen->longitude ?? 1.2123 + (rand(-100, 100) / 1000);
            $plainte->contact_telephone = $citoyen->telephone;
            $plainte->photo = null;

            // Lier à un signalement si disponible
            if ($signalements->isNotEmpty() && rand(0, 1)) {
                $plainte->signalement_id = $signalements->random()->id;
            }

            // Définir les dates selon le statut
            if ($statut === Plainte::STATUT_TRAITEE || $statut === Plainte::STATUT_FERMEE) {
                $plainte->date_traitement = now()->subDays(rand(1, 30));
                $plainte->traite_par = 1; // Admin par défaut
            }

            $plainte->created_at = now()->subDays(rand(0, 60));
            $plainte->updated_at = $plainte->created_at;
            $plainte->save();
        }

        $this->command->info('20 plaintes de test créées avec succès !');
    }
}
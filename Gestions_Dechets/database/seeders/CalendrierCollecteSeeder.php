<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CalendrierCollecte;
use App\Models\User;

class CalendrierCollecteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'administrateur pour être le responsable
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->warn('Aucun administrateur trouvé. Créez d\'abord un utilisateur administrateur.');
            return;
        }

        // Créer quelques calendriers de test simples
        $calendriersTest = [
            [
                'nom' => 'Collecte Ménagère - Centre-ville',
                'quartier' => 'Centre-ville',
                'type_collecte' => 'menagere',
                'frequence' => 'hebdomadaire',
                'jour_semaine' => 'lundi',
                'heure_debut' => '07:00:00',
                'heure_fin' => '12:00:00',
                'description' => 'Collecte hebdomadaire des déchets ménagers'
            ],
            [
                'nom' => 'Collecte Encombrant - Quartier Nord',
                'quartier' => 'Quartier Nord',
                'type_collecte' => 'encombrant',
                'frequence' => 'mensuelle',
                'jour_semaine' => 'samedi',
                'heure_debut' => '08:00:00',
                'heure_fin' => '16:00:00',
                'description' => 'Collecte mensuelle des encombrants'
            ],
            [
                'nom' => 'Collecte Vert - Quartier Sud',
                'quartier' => 'Quartier Sud',
                'type_collecte' => 'vert',
                'frequence' => 'hebdomadaire',
                'jour_semaine' => 'vendredi',
                'heure_debut' => '09:00:00',
                'heure_fin' => '14:00:00',
                'description' => 'Collecte hebdomadaire des déchets verts'
            ],
            [
                'nom' => 'Collecte Recyclage - Centre-ville',
                'quartier' => 'Centre-ville',
                'type_collecte' => 'recyclage',
                'frequence' => 'ponctuelle',
                'jour_semaine' => null,
                'heure_debut' => '10:00:00',
                'heure_fin' => '15:00:00',
                'description' => 'Collecte ponctuelle de recyclage'
            ]
        ];

        foreach ($calendriersTest as $data) {
            CalendrierCollecte::create([
                'nom' => $data['nom'],
                'quartier' => $data['quartier'],
                'type_collecte' => $data['type_collecte'],
                'frequence' => $data['frequence'],
                'jour_semaine' => $data['jour_semaine'],
                'heure_debut' => $data['heure_debut'],
                'heure_fin' => $data['heure_fin'],
                'date_debut' => now()->startOfMonth(),
                'date_fin' => now()->addMonths(3)->endOfMonth(),
                'description' => $data['description'],
                'statut' => 'actif',
                'responsable_id' => $admin->id,
            ]);
        }

        $this->command->info('Calendriers de collecte créés avec succès !');
    }
}
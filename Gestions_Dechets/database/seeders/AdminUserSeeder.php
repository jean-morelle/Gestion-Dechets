<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un administrateur
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@lome.tg',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'statut' => 'actif',
            'telephone' => '+228 22 21 20 19',
            'adresse' => 'Mairie de Lomé',
            'quartier' => 'Centre'
        ]);

        // Créer un citoyen de test
        User::create([
            'name' => 'Citoyen Test',
            'email' => 'citoyen@test.com',
            'password' => Hash::make('password'),
            'role' => 'citoyen',
            'statut' => 'actif',
            'telephone' => '+228 90 12 34 56',
            'adresse' => '123 Rue de la Paix',
            'quartier' => 'Agoè'
        ]);

        // Créer un collecteur de test
        User::create([
            'name' => 'Collecteur Test',
            'email' => 'collecteur@test.com',
            'password' => Hash::make('password'),
            'role' => 'collecteur',
            'statut' => 'actif',
            'telephone' => '+228 97 65 43 21',
            'adresse' => 'Dépôt de collecte',
            'quartier' => 'Adidogomé'
        ]);

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('Admin: admin@lome.tg / password');
        $this->command->info('Citoyen: citoyen@test.com / password');
        $this->command->info('Collecteur: collecteur@test.com / password');
    }
}
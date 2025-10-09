<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminUserSeeder::class,
            SignalementSeeder::class,
            PlainteSeeder::class,
            CalendrierCollecteSeeder::class,
            DemandeCollecteSeeder::class,
            CollecteSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
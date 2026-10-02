<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un compte créé via Google reçoit un mot de passe aléatoire que
     * l'utilisateur ne connaît pas. Cette colonne indique s'il en a choisi un.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('mot_de_passe_defini')->default(true)->after('password');
        });

        // Comptes Google existants : mot de passe aléatoire, jamais choisi
        DB::table('users')->whereNotNull('google_id')->update(['mot_de_passe_defini' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mot_de_passe_defini');
        });
    }
};

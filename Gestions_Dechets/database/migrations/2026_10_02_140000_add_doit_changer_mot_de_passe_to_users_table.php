<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Les comptes créés par l'administration (collecteurs, agents) reçoivent un
 * mot de passe provisoire, à remplacer dès la première connexion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('doit_changer_mot_de_passe')->default(false)->after('mot_de_passe_defini');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('doit_changer_mot_de_passe');
        });
    }
};

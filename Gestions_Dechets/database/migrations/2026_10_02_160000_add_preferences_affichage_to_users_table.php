<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Personnalisation de l'affichage, en plus du thème clair/sombre :
 * couleur principale de l'interface et taille du texte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('couleur_accent', 20)->default('vert')->after('theme');
            $table->string('taille_texte', 20)->default('normale')->after('couleur_accent');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['couleur_accent', 'taille_texte']);
        });
    }
};

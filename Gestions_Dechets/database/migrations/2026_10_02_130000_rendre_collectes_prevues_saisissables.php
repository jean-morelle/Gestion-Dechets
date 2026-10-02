<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Une collecte est créée « prévue » au démarrage de la tournée : la quantité
 * et le type de déchet ne sont connus qu'au passage du collecteur.
 * On ajoute aussi le motif d'échec et la précision GPS du passage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collectes', function (Blueprint $table) {
            $table->decimal('quantite', 10, 2)->nullable()->change();
            $table->enum('type_dechet', ['dechet_menager', 'dechet_vert', 'encombrant', 'dechet_dangereux', 'dechet_recyclable'])->nullable()->change();
            $table->string('motif_echec')->nullable()->after('notes');
            $table->unsignedInteger('precision_gps')->nullable()->after('longitude_fin')->comment('en mètres');
            $table->unsignedInteger('distance_point')->nullable()->after('precision_gps')->comment('écart en mètres entre le passage et le point');
        });
    }

    public function down(): void
    {
        Schema::table('collectes', function (Blueprint $table) {
            $table->dropColumn(['motif_echec', 'precision_gps', 'distance_point']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // D'abord, modifier la colonne pour accepter les nouvelles valeurs
        Schema::table('signalements', function (Blueprint $table) {
            $table->string('type_dechet', 50)->change();
        });
        
        // Ensuite, mettre à jour les données existantes
        DB::table('signalements')->where('type_dechet', 'menager')->update(['type_dechet' => 'dechet_menager']);
        DB::table('signalements')->where('type_dechet', 'vert')->update(['type_dechet' => 'dechet_vert']);
        DB::table('signalements')->where('type_dechet', 'dangereux')->update(['type_dechet' => 'dechet_dangereux']);
        
        // Enfin, remettre la contrainte enum
        Schema::table('signalements', function (Blueprint $table) {
            $table->enum('type_dechet', [
                'dechet_menager', 
                'dechet_vert', 
                'encombrant', 
                'dechet_dangereux', 
                'dechet_recyclable', 
                'autre'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // D'abord, modifier en string pour permettre le changement
        Schema::table('signalements', function (Blueprint $table) {
            $table->string('type_dechet', 50)->change();
        });
        
        // Remettre les données à l'ancien format
        DB::table('signalements')->where('type_dechet', 'dechet_menager')->update(['type_dechet' => 'menager']);
        DB::table('signalements')->where('type_dechet', 'dechet_vert')->update(['type_dechet' => 'vert']);
        DB::table('signalements')->where('type_dechet', 'dechet_dangereux')->update(['type_dechet' => 'dangereux']);
        
        // Revenir à l'ancienne structure
        Schema::table('signalements', function (Blueprint $table) {
            $table->enum('type_dechet', [
                'menager', 
                'vert', 
                'encombrant', 
                'dangereux'
            ])->change();
        });
    }
};
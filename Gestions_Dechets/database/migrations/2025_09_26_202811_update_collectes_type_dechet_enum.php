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
        // Temporarily change column to string to allow update
        Schema::table('collectes', function (Blueprint $table) {
            $table->string('type_dechet')->change();
        });

        // Update existing data
        DB::table('collectes')->where('type_dechet', 'menager')->update(['type_dechet' => 'dechet_menager']);
        DB::table('collectes')->where('type_dechet', 'vert')->update(['type_dechet' => 'dechet_vert']);
        DB::table('collectes')->where('type_dechet', 'dangereux')->update(['type_dechet' => 'dechet_dangereux']);
        DB::table('collectes')->where('type_dechet', 'recyclable')->update(['type_dechet' => 'dechet_recyclable']);

        // Change column to new enum values
        Schema::table('collectes', function (Blueprint $table) {
            $table->enum('type_dechet', ['dechet_menager', 'dechet_vert', 'encombrant', 'dechet_dangereux', 'dechet_recyclable'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum values if necessary, or handle data loss
        Schema::table('collectes', function (Blueprint $table) {
            $table->string('type_dechet')->change(); // Temporarily change to string
        });

        // Revert data if possible, or handle data loss
        DB::table('collectes')->where('type_dechet', 'dechet_menager')->update(['type_dechet' => 'menager']);
        DB::table('collectes')->where('type_dechet', 'dechet_vert')->update(['type_dechet' => 'vert']);
        DB::table('collectes')->where('type_dechet', 'dechet_dangereux')->update(['type_dechet' => 'dangereux']);
        DB::table('collectes')->where('type_dechet', 'dechet_recyclable')->update(['type_dechet' => 'recyclable']);

        Schema::table('collectes', function (Blueprint $table) {
            $table->enum('type_dechet', ['menager', 'vert', 'encombrant', 'dangereux', 'recyclable'])->change();
        });
    }
};
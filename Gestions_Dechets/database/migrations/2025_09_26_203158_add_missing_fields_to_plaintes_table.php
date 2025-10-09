<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plaintes', function (Blueprint $table) {
            $table->string('sujet')->after('type_plainte');
            $table->string('adresse')->after('description');
            $table->string('quartier')->after('adresse');
            $table->decimal('latitude', 10, 8)->nullable()->after('quartier');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('photo')->nullable()->after('longitude');
            $table->string('contact_telephone')->nullable()->after('photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plaintes', function (Blueprint $table) {
            $table->dropColumn([
                'sujet',
                'adresse', 
                'quartier',
                'latitude',
                'longitude',
                'photo',
                'contact_telephone'
            ]);
        });
    }
};
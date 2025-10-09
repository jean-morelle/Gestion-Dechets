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
        Schema::create('itineraire_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itineraire_id')->constrained()->onDelete('cascade');
            $table->foreignId('point_de_collecte_id')->constrained('point_de_collectes')->onDelete('cascade');
            $table->integer('ordre')->default(1);
            $table->timestamps();

            $table->unique(['itineraire_id', 'point_de_collecte_id']);
            $table->index(['itineraire_id', 'ordre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itineraire_points');
    }
};

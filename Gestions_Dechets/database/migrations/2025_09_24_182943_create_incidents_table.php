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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collecteur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('itineraire_id')->constrained('itineraires')->onDelete('cascade');
            $table->enum('type_incident', ['panne_vehicule', 'probleme_acces', 'dechet_non_collectable', 'autre']);
            $table->text('description');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('photo')->nullable();
            $table->enum('statut', ['signale', 'en_cours', 'resolu', 'annule'])->default('signale');
            $table->enum('priorite', ['urgente', 'elevee', 'normale', 'faible'])->default('normale');
            $table->text('admin_notes')->nullable();
            $table->timestamp('date_resolution')->nullable();
            $table->timestamps();

            $table->index(['collecteur_id', 'created_at']);
            $table->index(['itineraire_id', 'statut']);
            $table->index(['statut', 'priorite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};

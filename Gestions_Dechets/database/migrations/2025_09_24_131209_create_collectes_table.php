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
        Schema::create('collectes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itineraire_id')->constrained()->onDelete('cascade');
            $table->foreignId('point_collecte_id')->constrained('point_de_collectes')->onDelete('cascade');
            $table->foreignId('collecteur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('signalement_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type_dechet', ['menager', 'vert', 'encombrant', 'dangereux', 'recyclable']);
            $table->decimal('quantite', 10, 2);
            $table->string('unite_mesure', 10)->default('kg');
            $table->enum('statut', ['prevue', 'en_cours', 'termine', 'rate', 'annule'])->default('prevue');
            $table->date('date_collecte');
            $table->dateTime('heure_debut')->nullable();
            $table->dateTime('heure_fin')->nullable();
            $table->decimal('latitude_debut', 10, 8)->nullable();
            $table->decimal('longitude_debut', 11, 8)->nullable();
            $table->decimal('latitude_fin', 10, 8)->nullable();
            $table->decimal('longitude_fin', 11, 8)->nullable();
            $table->string('photo_avant')->nullable();
            $table->string('photo_apres')->nullable();
            $table->text('notes')->nullable();
            $table->json('incidents')->nullable();
            $table->decimal('distance_parcourue', 8, 2)->nullable();
            $table->integer('temps_collecte')->nullable()->comment('en minutes');
            $table->boolean('validation_gps')->default(false);
            $table->string('photo_validation')->nullable();
            $table->timestamps();

            $table->index(['statut', 'date_collecte']);
            $table->index(['collecteur_id']);
            $table->index(['itineraire_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collectes');
    }
};

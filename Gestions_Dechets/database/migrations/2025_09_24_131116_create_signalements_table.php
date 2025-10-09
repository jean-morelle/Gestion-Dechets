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
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type_dechet', ['menager', 'vert', 'encombrant', 'dangereux']);
            $table->text('description');
            $table->text('adresse');
            $table->string('quartier');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('photo')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'traite', 'annule'])->default('en_attente');
            $table->enum('priorite', ['faible', 'moyenne', 'elevee', 'urgente'])->default('moyenne');
            $table->dateTime('date_collecte_prevue')->nullable();
            $table->dateTime('date_collecte_reelle')->nullable();
            $table->foreignId('collecteur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes_admin')->nullable();
            $table->timestamps();

            $table->index(['statut', 'priorite']);
            $table->index(['quartier']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};

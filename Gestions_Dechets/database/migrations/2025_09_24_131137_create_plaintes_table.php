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
        Schema::create('plaintes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('signalement_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type_plainte', ['collecte_retard', 'collecte_oubliee', 'service_client', 'autre']);
            $table->text('description');
            $table->enum('statut', ['en_attente', 'en_cours', 'traite', 'ferme'])->default('en_attente');
            $table->enum('priorite', ['faible', 'moyenne', 'elevee', 'urgente'])->default('moyenne');
            $table->text('reponse')->nullable();
            $table->foreignId('traite_par')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_traitement')->nullable();
            $table->timestamps();

            $table->index(['statut', 'priorite']);
            $table->index(['type_plainte']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaintes');
    }
};

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
        Schema::create('itineraires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['quotidien', 'hebdomadaire', 'mensuel', 'ponctuel']);
            $table->text('description')->nullable();
            $table->foreignId('collecteur_id')->constrained('users')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->decimal('distance_estimee', 8, 2)->nullable();
            $table->integer('duree_estimee')->nullable()->comment('en minutes');
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['statut', 'type']);
            $table->index(['collecteur_id']);
            $table->index(['date_debut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itineraires');
    }
};

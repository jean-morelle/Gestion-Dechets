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
        Schema::create('calendrier_collectes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type_collecte', ['menagere', 'encombrant', 'vert', 'recyclage']);
            $table->string('quartier');
            $table->enum('frequence', ['quotidienne', 'hebdomadaire', 'mensuelle', 'ponctuelle']);
            $table->enum('jour_semaine', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'])->nullable();
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('description')->nullable();
            $table->enum('statut', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['statut', 'frequence']);
            $table->index(['quartier']);
            $table->index(['type_collecte']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendrier_collectes');
    }
};

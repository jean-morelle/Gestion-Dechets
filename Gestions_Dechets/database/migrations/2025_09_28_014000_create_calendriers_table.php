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
        Schema::create('calendriers', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('quartier');
            $table->enum('type_collecte', ['menagere', 'encombrant', 'vert', 'recyclage']);
            $table->enum('frequence', ['quotidienne', 'hebdomadaire', 'mensuelle', 'ponctuelle']);
            $table->string('jour_semaine')->nullable();
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('description')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendriers');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * L'ancien module de rappels (e-mail/SMS jamais envoyés) reposait sur une table
 * « calendriers » distincte du calendrier géré par l'administration et jamais
 * alimentée. Il est remplacé par les rappels automatiques de la veille
 * (commande collectes:rappeler, basée sur calendrier_collectes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('rappels');
        Schema::dropIfExists('calendriers');
    }

    public function down(): void
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

        Schema::create('rappels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('calendrier_id')->constrained('calendriers')->onDelete('cascade');
            $table->string('type_rappel')->default('email');
            $table->integer('delai_heures')->default(24);
            $table->boolean('actif')->default(true);
            $table->timestamp('derniere_envoi')->nullable();
            $table->timestamps();
        });
    }
};

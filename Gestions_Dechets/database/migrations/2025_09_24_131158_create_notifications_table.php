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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['signalement', 'plainte', 'paiement', 'collecte', 'itineraire', 'calendrier', 'systeme']);
            $table->string('titre');
            $table->text('message');
            $table->json('data')->nullable();
            $table->enum('statut', ['non_lu', 'lu', 'archive'])->default('non_lu');
            $table->enum('priorite', ['faible', 'moyenne', 'elevee', 'urgente'])->default('moyenne');
            $table->dateTime('date_envoi');
            $table->dateTime('date_lecture')->nullable();
            $table->foreignId('expediteur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('lien_action')->nullable();
            $table->string('icone')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'statut']);
            $table->index(['type', 'priorite']);
            $table->index(['date_envoi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

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
        Schema::create('campagnes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->enum('type', ['affiche', 'video', 'message', 'infographie']);
            $table->string('fichier')->nullable(); // Chemin vers le fichier
            $table->string('url_video')->nullable(); // URL de la vidéo
            $table->text('contenu_message')->nullable(); // Contenu du message
            $table->string('image_preview')->nullable(); // Image de prévisualisation
            $table->enum('statut', ['brouillon', 'active', 'terminee', 'archivee'])->default('brouillon');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->json('quartiers_cibles')->nullable(); // Quartiers ciblés
            $table->integer('vues')->default(0);
            $table->integer('partages')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagnes');
    }
};

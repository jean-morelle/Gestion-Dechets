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
        Schema::create('demande_collectes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Citoyen qui fait la demande
            $table->enum('type_collecte', ['menagere', 'encombrant', 'vert', 'recyclable', 'dangereux', 'demenagement'])->default('menagere');
            $table->string('objet');
            $table->text('description');
            $table->string('adresse');
            $table->string('quartier');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('urgence', ['faible', 'moyenne', 'elevee', 'urgente'])->default('moyenne');
            $table->date('date_souhaitee')->nullable();
            $table->time('heure_souhaitee')->nullable();
            $table->string('contact_telephone')->nullable();
            $table->text('instructions_speciales')->nullable();
            $table->string('photo')->nullable(); // Chemin vers la photo des déchets
            $table->decimal('montant_estime', 10, 2)->nullable(); // Estimation du coût
            $table->enum('statut', ['en_attente', 'en_cours', 'accepte', 'refuse', 'termine'])->default('en_attente');
            $table->text('raison_refus')->nullable(); // Raison si la demande est refusée
            $table->dateTime('date_traitement')->nullable(); // Date à laquelle la demande a été traitée par l'admin
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Admin qui traite la demande
            $table->foreignId('collecteur_id')->nullable()->constrained('users')->onDelete('set null'); // Collecteur assigné
            $table->dateTime('date_collecte_prevue')->nullable(); // Date de collecte prévue par l'admin/collecteur
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'statut']);
            $table->index(['quartier', 'type_collecte']);
            $table->index(['statut', 'urgence']);
            $table->index(['date_souhaitee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_collectes');
    }
};

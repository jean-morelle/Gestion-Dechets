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
        Schema::create('point_de_collectes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['public', 'prive', 'industriel', 'commercial']);
            $table->text('adresse');
            $table->string('quartier');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('capacite')->nullable();
            $table->enum('statut', ['actif', 'inactif', 'maintenance'])->default('actif');
            $table->text('description')->nullable();
            $table->time('horaires_ouverture')->nullable();
            $table->time('horaires_fermeture')->nullable();
            $table->string('contact_responsable')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['statut', 'type']);
            $table->index(['quartier']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_de_collectes');
    }
};

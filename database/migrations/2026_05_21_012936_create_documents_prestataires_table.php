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
        Schema::create('documents_prestataires', function (Blueprint $table) {
           $table->id();
        $table->foreignId('prestataire_id')->constrained('prestataires')->onDelete('cascade');
        $table->enum('type', ['cni', 'photo', 'preuve_activite', 'autre']);
        $table->string('chemin_fichier', 255);
        $table->string('nom_original', 255)->nullable();
        $table->integer('taille')->nullable();
        $table->enum('statut', ['pending', 'valide', 'rejete'])->default('pending');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_prestataires');
    }
};

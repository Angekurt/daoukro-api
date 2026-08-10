<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Intérêts pour les offres d'emploi.
 * Un citoyen connecté marque son intérêt pour une annonce de type 'emploi'.
 * Un citoyen ne peut marquer son intérêt qu'une seule fois par annonce.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interets_emploi', function (Blueprint $t) {
            $t->id();
            $t->foreignId('annonce_id')->constrained('annonces')->cascadeOnDelete();
            $t->foreignId('citoyen_id')->constrained('citoyens')->cascadeOnDelete();
            $t->timestamps();

            $t->unique(['annonce_id', 'citoyen_id']); // un seul intérêt par citoyen/offre
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interets_emploi');
    }
};

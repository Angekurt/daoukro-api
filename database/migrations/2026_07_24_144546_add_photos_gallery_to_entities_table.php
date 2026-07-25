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
        foreach (['pharmacies', 'hebergements', 'artisans'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                // Galerie complémentaire à la photo de couverture (`photo`) :
                // jusqu'à 4 images (façade pharmacie, chambres d'hôtel, réalisations artisan...).
                $t->json('photos')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['pharmacies', 'hebergements', 'artisans'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('photos');
            });
        }
    }
};

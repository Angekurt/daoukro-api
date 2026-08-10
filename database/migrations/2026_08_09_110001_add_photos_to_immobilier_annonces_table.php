<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute la colonne `photos` (galerie JSON) aux tables immobiliers et annonces.
 * La table artisans et hebergements l'ont déjà via la migration précédente.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['immobiliers', 'annonces'] as $table) {
            if (!Schema::hasColumn($table, 'photos')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->json('photos')->nullable()->after('photo');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['immobiliers', 'annonces'] as $table) {
            if (Schema::hasColumn($table, 'photos')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('photos');
                });
            }
        }
    }
};

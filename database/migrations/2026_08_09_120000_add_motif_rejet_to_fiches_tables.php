<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute motif_rejet sur les tables de fiches soumises depuis la PWA pro.
 * Permet à l'admin de saisir un motif lors du refus — envoyé par email
 * au professionnel via FicheObserver.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['artisans', 'hebergements', 'immobiliers', 'annonces'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('motif_rejet', 500)->nullable()->after('is_active');
            });
        }
    }

    public function down(): void
    {
        foreach (['artisans', 'hebergements', 'immobiliers', 'annonces'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('motif_rejet');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Même principe que pour artisans : rattache une fiche au citoyen qui
     * l'a soumise depuis la PWA pro. Nullable — les fiches créées depuis
     * le panel admin n'ont pas de citoyen associé.
     */
    public function up(): void
    {
        foreach (['hebergements', 'immobiliers', 'annonces'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreignId('citoyen_id')->nullable()->after('id')
                    ->constrained('citoyens')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['hebergements', 'immobiliers', 'annonces'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropConstrainedForeignId('citoyen_id');
            });
        }
    }
};

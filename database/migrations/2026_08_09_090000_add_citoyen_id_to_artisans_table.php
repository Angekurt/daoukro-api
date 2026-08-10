<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rattache une fiche artisan au citoyen qui l'a soumise depuis la PWA
     * pro (daoukro-pro). Nullable : les fiches créées depuis le panel admin
     * n'ont pas de citoyen associé.
     */
    public function up(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->foreignId('citoyen_id')->nullable()->after('id')
                ->constrained('citoyens')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('citoyen_id');
        });
    }
};

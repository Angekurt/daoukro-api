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
        foreach (['services_publics', 'annonces', 'actualites'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->json('photos')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['services_publics', 'annonces', 'actualites'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('photos');
            });
        }
    }
};

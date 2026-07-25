<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // admin (DG) : accès complet, y compris suppression et paramètres.
            // moderateur : gère le contenu au quotidien, ne peut ni supprimer
            // ni toucher aux paramètres globaux de l'application.
            $table->enum('role', ['admin', 'moderateur'])->default('moderateur')->after('email');
        });

        // Les comptes déjà existants avant l'introduction des rôles
        // conservent le plein accès (comportement identique à avant ce lot).
        DB::table('users')->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

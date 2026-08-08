<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Comptes des utilisateurs de l'app mobile (citoyens) — volontairement
     * séparés de `users` (réservée aux comptes admin/modérateur du panel
     * Filament) pour qu'un compte citoyen ne puisse jamais se connecter au
     * panel d'administration.
     */
    public function up(): void
    {
        Schema::create('citoyens', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citoyens');
    }
};

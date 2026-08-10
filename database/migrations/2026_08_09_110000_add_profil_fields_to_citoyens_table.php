<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute telephone et bio au profil citoyen (PWA daoukro-pro).
 * Ces champs sont optionnels — les citoyens existants ne sont pas affectés.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citoyens', function (Blueprint $t) {
            $t->string('telephone', 30)->nullable()->after('avatar_url');
            $t->string('bio', 500)->nullable()->after('telephone');
        });
    }

    public function down(): void
    {
        Schema::table('citoyens', function (Blueprint $t) {
            $t->dropColumn(['telephone', 'bio']);
        });
    }
};

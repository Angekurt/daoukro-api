<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les champs d'authentification email/téléphone aux citoyens.
 * Permet la connexion sans Google (email + mot de passe ou téléphone + nom).
 *
 * google_id reste nullable — les deux modes coexistent.
 * password nullable — seulement pour les comptes créés par email.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citoyens', function (Blueprint $t) {
            $t->string('password')->nullable()->after('email');
            $t->string('prenom', 100)->nullable()->after('name');
            $t->enum('auth_provider', ['google', 'email'])->default('google')->after('password');
            $t->boolean('est_actif')->default(true)->after('auth_provider');
            $t->string('statut', 20)->default('actif')->after('est_actif');
            // statut : actif | suspendu | en_veille
            $t->string('note_admin', 500)->nullable()->after('statut');
            $t->timestamp('email_verified_at')->nullable()->after('note_admin');
        });

        // google_id devient nullable (il l'est déjà, on s'assure juste)
        Schema::table('citoyens', function (Blueprint $t) {
            $t->string('google_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('citoyens', function (Blueprint $t) {
            $t->dropColumn(['password', 'prenom', 'auth_provider', 'est_actif', 'statut', 'note_admin', 'email_verified_at']);
        });
    }
};

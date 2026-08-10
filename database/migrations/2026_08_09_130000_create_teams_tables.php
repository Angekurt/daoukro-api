<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Comptes équipe pour la PWA daoukro-pro.
 *
 * Un établissement (hôtel, entreprise d'annonces...) peut créer une équipe
 * et inviter d'autres citoyens à gérer ses fiches :
 *   - teams           : l'équipe elle-même (propriétaire = citoyen créateur)
 *   - team_members    : membres invités + leur rôle (manager / editor)
 *   - team_invitations: invitations en attente par email
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('citoyens')->cascadeOnDelete();
            $t->string('nom', 150);
            $t->string('description', 500)->nullable();
            $t->string('logo')->nullable();
            $t->timestamps();
        });

        Schema::create('team_members', function (Blueprint $t) {
            $t->id();
            $t->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $t->foreignId('citoyen_id')->constrained('citoyens')->cascadeOnDelete();
            // manager : peut tout faire (créer/modifier/supprimer fiches de l'équipe)
            // editor  : peut modifier les fiches existantes, pas en créer
            $t->enum('role', ['manager', 'editor'])->default('editor');
            $t->timestamps();

            $t->unique(['team_id', 'citoyen_id']); // un citoyen = un seul rôle par équipe
        });

        Schema::create('team_invitations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $t->string('email', 150);
            $t->enum('role', ['manager', 'editor'])->default('editor');
            $t->string('token', 64)->unique();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();

            $t->unique(['team_id', 'email']); // une seule invitation active par email/équipe
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('teams');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plan d'abonnement du citoyen (professionnel PWA daoukro-pro).
 *
 * plan          : free | standard | pro | business
 * plan_expire_at: null = jamais (gratuit) ou date d'expiration
 * plan_details  : JSON libre — historique paiement, token MoneyFusion, etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citoyens', function (Blueprint $t) {
            $t->string('plan', 20)->default('free')->after('bio');
            $t->timestamp('plan_expire_at')->nullable()->after('plan');
            $t->json('plan_details')->nullable()->after('plan_expire_at');
        });
    }

    public function down(): void
    {
        Schema::table('citoyens', function (Blueprint $t) {
            $t->dropColumn(['plan', 'plan_expire_at', 'plan_details']);
        });
    }
};

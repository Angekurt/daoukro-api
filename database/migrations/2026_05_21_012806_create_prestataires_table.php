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
        Schema::create('prestataires', function (Blueprint $table) {
                   $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('ville_id')->constrained('villes')->onDelete('cascade');
        $table->foreignId('categorie_metier_id')->constrained('categories_metiers')->onDelete('cascade');
        $table->string('nom_complet', 150);
        $table->string('telephone', 20);
        $table->text('description')->nullable();
        $table->text('zone_intervention')->nullable();
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->enum('statut', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('note_admin')->nullable();
        $table->timestamp('approved_at')->nullable();
        $table->timestamp('rejected_at')->nullable();
        $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestataires');
    }
};

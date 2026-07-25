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
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->enum('categorie', ['voirie', 'eclairage', 'dechets', 'eau', 'securite', 'autre']);
            $table->text('description');
            $table->string('adresse')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('auteur')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'resolu'])->default('en_attente');
            $table->text('note_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};

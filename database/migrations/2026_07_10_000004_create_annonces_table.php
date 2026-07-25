<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->constrained('villes')->onDelete('cascade');
            $table->string('titre', 200);
            $table->text('description');
            $table->enum('type', ['evenement', 'emploi', 'restaurant', 'pub', 'annonce'])->default('annonce');
            $table->string('categorie', 100)->nullable();
            $table->string('auteur', 150)->nullable();
            $table->string('lieu', 255)->nullable();
            $table->string('date_debut', 100)->nullable();
            $table->string('date_fin', 100)->nullable();
            $table->string('contact', 50)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('lien', 500)->nullable();
            $table->string('photo', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};

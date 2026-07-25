<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('annonces');
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->nullOnDelete();
            $table->string('titre');
            $table->text('description');
            $table->enum('type', ['evenement', 'emploi', 'restaurant', 'pub', 'annonce'])->default('annonce');
            $table->string('categorie')->nullable();
            $table->string('auteur')->nullable();
            $table->string('lieu')->nullable();
            $table->string('date_debut')->nullable();
            $table->string('date_fin')->nullable();
            $table->string('contact', 30)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('lien')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};

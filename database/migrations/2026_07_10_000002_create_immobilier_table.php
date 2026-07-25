<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('immobilier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->constrained('villes')->onDelete('cascade');
            $table->string('titre', 200);
            $table->enum('type_offre', ['vente', 'location'])->default('vente');
            $table->enum('type_bien', ['maison', 'terrain', 'appartement', 'villa', 'bureau', 'autre'])->default('maison');
            $table->text('description')->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('quartier', 150)->nullable();
            $table->decimal('prix', 15, 0)->default(0);
            $table->string('surface', 50)->nullable();
            $table->integer('nb_chambres')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('photo', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immobilier');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('immobiliers');
        Schema::create('immobiliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->nullOnDelete();
            $table->string('titre');
            $table->enum('type_offre', ['vente', 'location'])->default('vente');
            $table->enum('type_bien', ['maison', 'terrain', 'appartement', 'villa', 'bureau'])->default('maison');
            $table->text('description')->nullable();
            $table->string('adresse')->nullable();
            $table->string('quartier')->nullable();
            $table->decimal('prix', 15, 0)->default(0);
            $table->string('surface')->nullable();
            $table->integer('nb_chambres')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immobiliers');
    }
};

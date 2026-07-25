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
        Schema::create('services_publics', function (Blueprint $table) {
           $table->id();
        $table->foreignId('ville_id')->constrained('villes')->onDelete('cascade');
        $table->foreignId('categorie_id')->constrained('categories_services')->onDelete('cascade');
        $table->string('nom', 150);
        $table->text('description')->nullable();
        $table->string('adresse', 255)->nullable();
        $table->string('telephone', 20)->nullable();
        $table->string('email', 150)->nullable();
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->text('horaires')->nullable();
        $table->string('photo', 255)->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_publics');
    }
};

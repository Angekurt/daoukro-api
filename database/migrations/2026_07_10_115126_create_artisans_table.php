<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('artisans');
        Schema::create('artisans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->nullable()->constrained('villes')->nullOnDelete();
            $table->string('nom');
            $table->string('metier');
            $table->text('description')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('photo')->nullable();
            $table->decimal('note', 3, 1)->nullable();
            $table->integer('nb_avis')->default(0);
            $table->boolean('disponible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisans');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hebergements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->constrained('villes')->onDelete('cascade');
            $table->string('nom', 150);
            $table->enum('type', ['hotel', 'residence', 'meuble', 'autre'])->default('hotel');
            $table->text('description')->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('prix_min', 10, 0)->nullable();
            $table->decimal('prix_max', 10, 0)->nullable();
            $table->string('photo', 500)->nullable();
            $table->decimal('note', 3, 1)->nullable();
            $table->integer('nb_avis')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hebergements');
    }
};

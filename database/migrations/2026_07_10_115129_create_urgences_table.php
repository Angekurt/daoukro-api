<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('urgences');
        Schema::create('urgences', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('categorie', ['sante', 'securite', 'incendie', 'autre'])->default('autre');
            $table->string('telephone', 20);
            $table->string('telephone2', 20)->nullable();
            $table->string('adresse')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urgences');
    }
};

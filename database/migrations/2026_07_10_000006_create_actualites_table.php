<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actualites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->constrained('villes')->onDelete('cascade');
            $table->string('titre', 250);
            $table->text('contenu');
            $table->enum('categorie', ['mairie', 'alerte', 'sante', 'info', 'culture', 'autre'])->default('info');
            $table->string('photo', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actualites');
    }
};

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
        Schema::create('gardes', function (Blueprint $table) {
            $table->id();
        $table->foreignId('pharmacie_id')->constrained('pharmacies')->onDelete('cascade');
        $table->date('date_debut');
        $table->date('date_fin');
        $table->text('note')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gardes');
    }
};

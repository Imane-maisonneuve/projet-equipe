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
        Schema::create('vins', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('nom')->nullable();
            $table->float('prix')->nullable();
            $table->string('pays')->nullable();
            $table->string('region')->nullable();
            $table->string('cepage')->nullable();
            $table->string('degre_alcool')->nullable();
            $table->string('taux_sucre')->nullable();
            $table->string('format')->nullable();
            $table->string('annee')->nullable();
            $table->string('image_url')->nullable();
            $table->string('couleur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vins');
    }
};

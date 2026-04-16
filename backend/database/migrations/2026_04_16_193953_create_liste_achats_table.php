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
        Schema::create('liste_achats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usager_id');
            $table->foreign('usager_id')->references('id')->on('usagers')->onDelete('cascade');
            $table->unsignedBigInteger('vin_id');
            $table->foreign('vin_id')->references('id')->on('vins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liste_achats');
    }
};

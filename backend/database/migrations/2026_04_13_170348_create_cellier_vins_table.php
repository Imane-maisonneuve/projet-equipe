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
        Schema::create('cellier_vins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cellier_id');
            $table->foreign('cellier_id')->references('id')->on('celliers')->onDelete('cascade');
            $table->unsignedBigInteger('vin_id');
            $table->foreign('vin_id')->references('id')->on('vins')->onDelete('cascade');
            $table->integer('quantite')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cellier_vins');
    }
};

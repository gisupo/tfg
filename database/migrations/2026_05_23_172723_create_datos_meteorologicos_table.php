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
        Schema::create('datos_meteorologicos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora');
            $table->float('temperatura')->nullable();
            $table->float('humedad')->nullable();
            $table->float('velocidad_viento')->nullable();
            $table->float('direccion_viento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_meteorologicos');
    }
};

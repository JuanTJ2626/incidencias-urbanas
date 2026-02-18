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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_ciudadano', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion');
            $table->decimal('latitud', 10,6)->nullable();
            $table->decimal('longitud', 10,6)->nullable();
            $table->string('tipo_incidencia');
            $table->string('descripcion')->nullable();
            $table->string('estatus');
            $table->string('foto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};

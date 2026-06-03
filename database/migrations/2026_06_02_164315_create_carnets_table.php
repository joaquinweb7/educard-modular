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
        Schema::create('carnets', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cedula_identidad')->unique();
            $table->string('codigo_estudiante')->unique();
            $table->string('fecha_emision');
            $table->string('fecha_caducidad');
            $table->string('carrera');
            $table->string('semestre');
            $table->string('observacion')->nullable();
            $table->string('estado')->default('VIGENTE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnets');
    }
};

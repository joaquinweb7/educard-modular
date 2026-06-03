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
        Schema::create('credenciales', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cedula_identidad')->unique();
            $table->string('codigo_credencial')->unique();
            $table->string('cargo_principal');
            $table->string('cargo_secundario')->nullable();
            $table->string('departamento')->nullable();
            $table->string('fecha_emision');
            $table->string('fecha_caducidad');
            $table->string('observacion')->nullable();
            $table->string('estado')->default('VIGENTE');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credenciales');
    }
};

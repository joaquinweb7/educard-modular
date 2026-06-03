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
        Schema::create('certificados_plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('imagen');
            $table->integer('nombre_estudiante_x')->default('0')->nullable();
            $table->integer('nombre_estudiante_y')->default('90')->nullable();
            $table->integer('nombre_curso_x')->default('75')->nullable();
            $table->integer('nombre_curso_y')->default('123')->nullable();
            $table->integer('qr_x')->default('210')->nullable();
            $table->integer('qr_y')->default('14')->nullable();
            $table->integer('codigo_x')->default('210')->nullable();
            $table->integer('codigo_y')->default('60')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados_plantillas');
    }
};

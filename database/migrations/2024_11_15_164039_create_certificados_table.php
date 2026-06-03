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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_estudiante');
            $table->foreignId('nombre_curso_id')->constrained('certificados_cursos', 'id')->onDelete('cascade');
            $table->string('carnet');
            $table->string('email');
            $table->string('codigo');
            $table->foreignId('plantilla_id')->constrained('certificados_plantillas', 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};

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
        Schema::create('carnet_photo_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carnet_id')->constrained('carnets')->onDelete('cascade');
            $table->string('codigo_estudiante');
            $table->string('photo_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnet_photo_updates');
    }
};

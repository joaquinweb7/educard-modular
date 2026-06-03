<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_requests', function (Blueprint $table) {
            $table->id();
            $table->string('procedure_number')->unique();
            $table->string('names');
            $table->string('lastnames');
            $table->string('ci_number');
            $table->foreignId('career_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('semester_id')->constrained()->cascadeOnUpdate();
            $table->string('photo_path');
            $table->string('status')->default('pending')->index();
            $table->text('observation')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_requests');
    }
};

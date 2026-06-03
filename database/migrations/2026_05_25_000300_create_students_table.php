<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_code_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->unique();
            $table->unsignedInteger('last_number')->default(99);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_code')->unique();
            $table->string('names');
            $table->string('lastnames');
            $table->string('ci_number')->unique();
            $table->foreignId('career_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('semester_id')->constrained()->cascadeOnUpdate();
            $table->string('photo_path')->nullable();
            $table->string('status')->default('active')->index();
            $table->foreignId('approved_request_id')->nullable()->constrained('student_requests')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('student_code_sequences');
    }
};

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
        Schema::table('student_requests', function (Blueprint $table) {
            $table->string('gestion')->nullable()->after('semester_id');
            $table->string('turno')->nullable()->after('gestion');
            $table->string('grupo')->nullable()->after('turno');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('gestion')->nullable()->after('semester_id');
            $table->string('turno')->nullable()->after('gestion');
            $table->string('grupo')->nullable()->after('turno');
        });
    }

    public function down(): void
    {
        Schema::table('student_requests', function (Blueprint $table) {
            $table->dropColumn(['gestion', 'turno', 'grupo']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['gestion', 'turno', 'grupo']);
        });
    }
};

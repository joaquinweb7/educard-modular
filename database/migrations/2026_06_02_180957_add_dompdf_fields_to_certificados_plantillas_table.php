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
        Schema::table('certificados_plantillas', function (Blueprint $table) {
            $table->longText('design_json')->nullable()->after('imagen');
            $table->integer('width')->default(1056)->after('design_json');
            $table->integer('height')->default(816)->after('width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificados_plantillas', function (Blueprint $table) {
            $table->dropColumn(['design_json', 'width', 'height']);
        });
    }
};

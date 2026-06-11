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
        Schema::table('exam_components', function (Blueprint $table) {
            $table->integer('min_marks')->default(0)->after('max_marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_components', function (Blueprint $table) {
            $table->dropColumn(['min_marks']);
        });
    }
};

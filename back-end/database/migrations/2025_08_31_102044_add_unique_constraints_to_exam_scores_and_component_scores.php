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
        // إضافة unique constraint في exam_scores
        Schema::table('exam_scores', function (Blueprint $table) {
            $table->unique(['exam_id', 'student_id'], 'exam_scores_exam_student_unique');
        });

        // إضافة unique constraint في exam_component_scores
        Schema::table('exam_component_scores', function (Blueprint $table) {
            $table->unique(['student_id', 'exam_component_id'], 'exam_component_scores_student_component_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_scores', function (Blueprint $table) {
            $table->dropUnique('exam_scores_exam_student_unique');
        });

        Schema::table('exam_component_scores', function (Blueprint $table) {
            $table->dropUnique('exam_component_scores_student_component_unique');
        });
    }
};

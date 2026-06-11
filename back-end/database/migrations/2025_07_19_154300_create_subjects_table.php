<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('academic_year_id');
                $table->foreign('teacher_id')
                    ->references('id')
                    ->on('teachers')
                    ->onDelete('set null');
            }

            if (! Schema::hasColumn('subjects', 'academic_year_id')) {
                $table->unsignedBigInteger('academic_year_id')->nullable()->after('weekly_sessions');
                $table->foreign('academic_year_id')
                    ->references('id')
                    ->on('academic_years')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'teacher_id')) {
                $table->dropForeign(['teacher_id']);
                $table->dropColumn('teacher_id');
            }

            if (Schema::hasColumn('subjects', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
        });
    }
};

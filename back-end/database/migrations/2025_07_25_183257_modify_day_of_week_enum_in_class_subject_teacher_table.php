<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyDayOfWeekEnumInClassSubjectTeacherTable extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class_rooms_subject_teachers')) {
            DB::statement("
                ALTER TABLE `class_rooms_subject_teachers`
                MODIFY COLUMN `day_of_week`
                ENUM('sunday','monday','tuesday','wednesday','thursday','friday')
                NOT NULL DEFAULT 'sunday'
            ");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('class_rooms_subject_teachers')) {
            DB::statement("
                ALTER TABLE `class_rooms_subject_teachers`
                MODIFY COLUMN `day_of_week`
                ENUM(''sunday',monday','tuesday','wednesday','thursday','friday')
                NOT NULL DEFAULT 'sunday'
            ");
        }
    }
}

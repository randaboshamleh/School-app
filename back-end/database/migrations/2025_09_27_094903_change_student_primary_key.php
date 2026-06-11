<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // إذا فيه Index باسم student_id نحذفه أول شي
            $table->dropIndex('student_id');

            // نعدل عمود id ونشيله من AUTO_INCREMENT
            DB::statement('ALTER TABLE students MODIFY id INT UNSIGNED;');

            // نضيف مفتاح أساسي جديد على student_id
            $table->primary('student_id', 'pk_student_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // نحذف المفتاح الأساسي الجديد
            $table->dropPrimary('pk_student_id');

            // نرجع المفتاح الأساسي على id
            $table->primary('id');

            // نرجع AUTO_INCREMENT
            DB::statement('ALTER TABLE students MODIFY id INT UNSIGNED AUTO_INCREMENT;');
        });
    }
};

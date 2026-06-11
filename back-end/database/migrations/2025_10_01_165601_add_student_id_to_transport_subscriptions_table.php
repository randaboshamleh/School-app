<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            // أضف العمود بنفس نوع students.id
            if (! Schema::hasColumn('transport_subscribtions', 'student_id')) {
                $table->unsignedBigInteger('student_id')->after('id');

                // أضف العلاقة مع students
                $table->foreign('student_id', 'ts_student_fk')
                    ->references('id')
                    ->on('students')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            if (Schema::hasColumn('transport_subscribtions', 'student_id')) {
                $table->dropForeign('ts_student_fk');
                $table->dropColumn('student_id');
            }
        });
    }
};

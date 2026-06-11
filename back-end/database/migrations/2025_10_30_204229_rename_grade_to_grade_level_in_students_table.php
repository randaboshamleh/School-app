<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // إعادة التسمية فقط إذا كان العمود موجود
        if (Schema::hasColumn('students', 'grade')) {
            Schema::table('students', function (Blueprint $table) {
                $table->renameColumn('grade', 'grade_level');
            });
        }

        // تعديل نوع العمود والتأكد أنه ليس null
        if (Schema::hasColumn('students', 'grade_level')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('grade_level')->nullable(false)->change();
            });
        }
    }

    public function down()
    {
        // عكس العملية في حال rollback
        if (Schema::hasColumn('students', 'grade_level')) {
            Schema::table('students', function (Blueprint $table) {
                $table->renameColumn('grade_level', 'grade');
            });
        }

        if (Schema::hasColumn('students', 'grade')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('grade')->nullable()->change();
            });
        }
    }
};

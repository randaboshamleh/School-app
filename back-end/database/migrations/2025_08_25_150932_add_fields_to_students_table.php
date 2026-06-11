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
        Schema::table('students', function (Blueprint $table) {
            $table->string('full_name');
            $table->string('nationality');
            $table->string('class');
            $table->string('section');
            $table->string('student_number')->unique();
            $table->float('gpa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students_table', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'nationality',
                'class',
                'section',
                'student_number',
                'gpa',
            ]);
        });
    }
};

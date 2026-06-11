<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // نحذف العمود id نهائياً
            $table->dropColumn('id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // نرجع العمود id إذا احتجنا rollback
            $table->integer('id')->unsigned()->autoIncrement();
        });
    }
};

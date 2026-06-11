<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            // أولاً تأكد أن العمود موجود
            if (Schema::hasColumn('transport_subscribtions', 'academic_year_id')) {
                // اجعل العمود NOT NULL (إذا ما عدّلته قبل)
                $table->unsignedBigInteger('academic_year_id')->nullable(false)->change();

                // أضف الـ Foreign Key
                $table->foreign('academic_year_id', 'ts_academic_year_fk')
                    ->references('id')
                    ->on('academic_years')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            if (Schema::hasColumn('transport_subscribtions', 'academic_year_id')) {
                $table->dropForeign('ts_academic_year_fk');
            }
        });
    }
};

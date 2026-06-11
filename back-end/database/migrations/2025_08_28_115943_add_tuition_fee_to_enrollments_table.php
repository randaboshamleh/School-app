<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // إضافة عمود الرسوم الدراسية
            $table->decimal('tuition_fee', 10, 2)->nullable()
                ->comment('الرسوم الدراسية الكاملة قبل أي منحة');

            // تأكد إن عمود نسبة المنحة موجود
            if (! Schema::hasColumn('enrollments', 'scholarship_percentage')) {
                $table->unsignedInteger('scholarship_percentage')->default(0)
                    ->comment('نسبة المنحة من 0 إلى 100');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['tuition_fee', 'scholarship_percentage']);
        });
    }
};

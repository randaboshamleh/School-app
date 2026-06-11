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
        if (! Schema::hasTable('class_rooms_subject_teachers')) {
            Schema::create('class_rooms_subject_teachers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_rooms_id')->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained()->onDelete('cascade');

                $table->integer('periods_per_week')->default(30);       // عدد الحصص أسبوعياً
                $table->integer('minutes_per_period')->default(45);     // مدة كل حصة

                $table->string('room')->nullable();                     // رقم القاعة أو المكان
                $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'])->nullable();
                // تحديد يوم الحصة الافتراضي
                $table->time('start_time')->nullable();                 // وقت بدء الحصة مثال: 08:00
                $table->time('end_time')->nullable();                   // وقت انتهاء الحصة
                $table->string('semester')->nullable();                 // لتحديد الفصل الدراسي: "Fall", "Spring" أو "Term 1"
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_room_subject_teachers');
    }
};

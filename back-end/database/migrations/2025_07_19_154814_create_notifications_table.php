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
        Schema::create('notifications', function (Blueprint $table) {

            $table->uuid('id')->primary();               // هوية الإشعار
            $table->string('type');                      // اسم الكلاس المسؤول عن الإشعار
            $table->morphs('notifiable');                // أعمدتان: notifiable_type و notifiable_id
            $table->text('data');                        // بيانات الإشعار بصيغة JSON
            $table->timestamp('read_at')->nullable();    // تاريخ تمييز الإشعار كمقروء
            $table->timestamps();      // created_at و updated_at :contentReference[oaicite:1]{index=1}
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['admin', 'student']);
            $table->string('guard_name')->default('web');         // حارس مصادقة جارد (web، api) [spatie convention]
            $table->string('display_name')->nullable();           // اسم قابل للعرض (مثل "مدير النظام")
            $table->text('description')->nullable();              // وصف مختصر للدور
            $table->boolean('is_default')->default(false);        // هل يُعطي تلقائيًا عند التسجيل؟
            $table->boolean('is_protected')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

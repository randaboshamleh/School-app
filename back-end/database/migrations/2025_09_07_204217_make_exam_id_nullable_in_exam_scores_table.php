<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_scores', function (Blueprint $table) {
            // نجعل exam_id nullable
            $table->unsignedBigInteger('exam_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('exam_scores', function (Blueprint $table) {
            // نرجعه إجباري إذا رجعنا rollback
            $table->unsignedBigInteger('exam_id')->nullable(false)->change();
        });
    }
};

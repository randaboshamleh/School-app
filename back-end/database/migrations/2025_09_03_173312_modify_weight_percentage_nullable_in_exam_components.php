<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_components', function (Blueprint $table) {
            $table->integer('weight_percentage')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('exam_components', function (Blueprint $table) {
            $table->integer('weight_percentage')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (from UUID back to BIGINT auto increment).
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // نحذف العمود الحالي (UUID)
            $table->dropColumn('id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            // نرجعه BIGINT AUTO INCREMENT Primary Key
            $table->bigIncrements('id')->first();
        });
    }

    /**
     * Reverse the migrations (from BIGINT back to UUID).
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
        });
    }
};

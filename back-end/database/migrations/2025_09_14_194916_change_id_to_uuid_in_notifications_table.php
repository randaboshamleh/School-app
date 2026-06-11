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
        // ⚠️ تغيير نوع العمود من INT إلى UUID
        Schema::table('notifications', function (Blueprint $table) {
            // لازم تكون مركّب doctrine/dbal علشان تشتغل change()
            $table->uuid('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // رجع العمود إلى Big Integer (زي ما كان بالأول)
            $table->bigIncrements('id')->change();
        });
    }
};

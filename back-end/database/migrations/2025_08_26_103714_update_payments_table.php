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
        Schema::table('payments', function (Blueprint $table) {
            $table->date('payment_date')->nullable()->change();
            $table->date('due_date')->nullable()->change();
            $table->boolean('is_paid')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->date('payment_date')->default(now())->change();
            $table->date('due_date')->nullable(false)->change();
            $table->boolean('is_paid')->default(true)->change();
        });
    }
};

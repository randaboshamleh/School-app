<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            // 🔹 أضف academic_year_id
            if (! Schema::hasColumn('transport_subscribtions', 'academic_year_id')) {
                $table->unsignedBigInteger('academic_year_id')->after('route_id');
            }

            // 🔹 عدّل status → is_active
            if (Schema::hasColumn('transport_subscripbions', 'status')) {
                $table->dropColumn('status');
            }

            if (! Schema::hasColumn('transport_subscribtions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('academic_year_id');
            }

            // 🔹 عدّل unique (ضيف السنة مع الاشتراك)
            $table->unique(['user_id', 'route_id', 'academic_year_id'], 'transport_unique_user_route_year');
        });
    }

    public function down(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            // رجع status مكان is_active
            if (Schema::hasColumn('transport_subscribtions', 'is_active')) {
                $table->dropColumn('is_active');
                $table->enum('status', ['active', 'canceled'])->default('active');
            }

            if (Schema::hasColumn('transport_subscribtions', 'academic_year_id')) {
                $table->dropColumn('academic_year_id');
            }

            $table->dropUnique(['user_id', 'route_id', 'academic_year_id']);
            $table->unique(['user_id', 'route_id']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('transport_subscriptions', 'user_id')) {
                // أول شي نحذف الـ unique index
                $table->dropUnique('transport_subscriptions_user_id_route_id_unique');

                // بعدين نحذف العمود نفسه
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transport_subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_subscriptions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();

                // رجعنا الـ unique كمان إذا رجع العمود
                $table->unique(['user_id', 'route_id'], 'transport_subscriptions_user_id_route_id_unique');
            }
        });
    }
};

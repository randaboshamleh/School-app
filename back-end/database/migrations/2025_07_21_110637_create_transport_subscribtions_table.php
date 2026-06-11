<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_subscribtions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('transport_subscribtions', 'route_id')) {
                $table->unsignedBigInteger('route_id')->after('user_id');
                $table->foreign('route_id')
                    ->references('id')
                    ->on('transport_routes')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('transport_subscribtions', 'status')) {
                $table->enum('status', ['active', 'canceled'])
                    ->default('active')
                    ->after('route_id');
            }

            // أضف اليوتيك مباشرة
            $table->unique(['user_id', 'route_id']);
        });
    }

    public function down(): void
    {
        Schema::table('transport_subscribtions', function (Blueprint $table) {
            if (Schema::hasColumn('transport_subscribtions', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('transport_subscribtions', 'route_id')) {
                $table->dropForeign(['route_id']);
                $table->dropColumn('route_id');
            }

            if (Schema::hasColumn('transport_subscribtions', 'status')) {
                $table->dropColumn('status');
            }

            $table->dropUnique(['user_id', 'route_id']);
        });
    }
};

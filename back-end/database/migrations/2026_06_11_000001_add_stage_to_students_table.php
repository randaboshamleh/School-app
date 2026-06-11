<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('students', 'stage')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('stage')->nullable()->after('section');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'stage')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('stage');
            });
        }
    }
};

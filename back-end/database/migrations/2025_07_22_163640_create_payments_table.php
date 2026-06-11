<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();

            $table->date('payment_date')->default(now());
            $table->decimal('amount', 11, 2);
            $table->enum('method', ['cash', 'card', 'bank_transfer'])->default('cash');

            $table->date('due_date')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->string('reference')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

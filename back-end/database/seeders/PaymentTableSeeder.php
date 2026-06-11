<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = Enrollment::all();

        foreach ($enrollments as $enrollment) {
            // الإدخالات العشوائية: 1 إلى 3 دفعات لكل تسجيل
            foreach (range(1, rand(1, 3)) as $i) {
                Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'payment_date' => now()->subDays(rand(0, 365)),
                    'amount' => rand(1000000 * 100, 10000000 * 100) / 100,
                    'method' => ['cash', 'card', 'bank_transfer'][rand(0, 2)],
                    'reference' => 'REF'.strtoupper(bin2hex(random_bytes(6))), // هي معرّف فريد للدفعة،
                ]);
            }
        }

    }
}

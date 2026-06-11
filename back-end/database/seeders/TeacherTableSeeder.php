<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherUsers = User::whereBetween('id', [1, 100])->get();

        foreach ($teacherUsers as $user) {
            Teacher::updateOrCreate(
                ['first_name' => 'أ. أحمد',
                    'last_name' => 'سامي',
                    'specialization' => 'عام',      // أو تخصص فعلي
                    'hire_date' => now()->subYears(rand(1, 5)),
                    'phone_number' => '09'.rand(10000000, 99999999),

                ]
            );
        }
    }
}

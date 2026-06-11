<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentTableSeeder extends Seeder
{
    public function run(): void
    {
        // طالب 1
        $user1 = User::updateOrCreate([
            'name' => 'رند ابو شملة',
            'student_id' => '1001',
            'password' => Hash::make('123456'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user1->id,
            'student_id' => '1001',
            'name' => 'رند ابو شملة',
            'class' => 'التاسع',
            'section' => 'A',
            'address' => 'الميدان',
            'gender' => 'انثى',
            'status' => 'active',
            'nationality' => 'سورية',
            'gpa' => '88',
        ]);

        // طالب 2
        $user2 = User::updateOrCreate([
            'name' => 'سارة علي',
            'student_id' => '1002',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user2->id,
            'student_id' => '1002',
            'name' => 'سارة علي',
            'class' => 'العاشر',
            'section' => 'B',
            'address' => 'التجارة',
            'gender' => 'انثى',
            'status' => 'active',
            'nationality' => 'سورية',
            'gpa' => '91',
        ]);

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'student_id' => '1003',
                'password' => Hash::make('admin123'),
                'role' => 'admin',

            ]
        );
    }
}

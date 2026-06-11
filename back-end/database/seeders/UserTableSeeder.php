<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['guard_name' => 'sanctum']);

        // إنشاء المستخدم أو استرجاعه بناءً على البريد
        $user = User::firstOrCreate(
            ['name' => 'رند ابو شملة', 'student_id' => '1001', 'role' => 'student', 'password' => Hash::make('123456'), 'role_id' => $studentRole->id]
        );

        $AdminRole = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'sanctum']);

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'System Admin', 'student_id' => '1003', 'role' => 'admin', 'password' => Hash::make('admin123'), 'role_id' => $AdminRole->id]
        );
    }
}

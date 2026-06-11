<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupervisorTableSeeder extends Seeder
{
    public function run()
    {
        $supervisors = [
            ['name' => 'موجه ابتدائي ذكور',  'password' => '12345678', 'student_id' => '1004', 'role' => 'primary_male_supervisor', 'role_id' => '9', 'is_admin' => '1'],
            ['name' => 'موجه ابتدائي إناث',  'password' => '12345678', 'student_id' => '1005',  'role' => 'primary_female_supervisor', 'role_id' => '8', 'is_admin' => '1'],
            ['name' => 'موجه اعدادي ذكور',  'password' => '12345678', 'student_id' => '1006',  'role' => 'secondary_male_supervisor', 'role_id' => '11', 'is_admin' => '1'],
            ['name' => 'موجه اعدادي إناث',  'password' => '12345678', 'student_id' => '1007',  'role' => 'secondary_female_supervisor', 'role_id' => '10', 'is_admin' => '1'],
            ['name' => 'موجه ثانوي ذكور',  'password' => '12345678', 'student_id' => '1008',  'role' => 'thirdy_male_supervisor', 'role_id' => '13', 'is_admin' => '1'],
            ['name' => 'موجه ثانوي إناث', 'password' => '12345678', 'student_id' => '1009',  'role' => 'thirdy_female_supervisor', 'role_id' => '12', 'is_admin' => '1'],
        ];

        foreach ($supervisors as $sup) {
            $user = User::create([
                'name' => $sup['name'],
                'student_id' => $sup['student_id'],
                'password' => Hash::make($sup['password']),
                'role' => $sup['role'],
                'role_id' => $sup['role_id'],
                'is_admin' => $sup['is_admin'],
            ]);

            $user->assignRole($sup['role']);
        }
    }
}

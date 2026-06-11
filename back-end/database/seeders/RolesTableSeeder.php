<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(['name' => 'admin'],
            [
                'guard_name' => 'sanctum',
                'display_name' => 'مدير النظام',
                'description' => 'صلاحيات كاملة لإدارة المنصة',
                'is_default' => false,
                'is_protected' => true,  // لا يسمح بحذفه أو تغيير اسمه لاحقًا
            ]
        );

        Role::updateOrCreate(['name' => 'student'],
            [
                'guard_name' => 'sanctum',
                'display_name' => 'طالب',
                'description' => 'صلاحية الوصول للمحتوى كطالب',
                'is_default' => true,  // يُمنح تلقائيًا للمستخدمين الجدد
                'is_protected' => false,
            ]
        );

    }
}

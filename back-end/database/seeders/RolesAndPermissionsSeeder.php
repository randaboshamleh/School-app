<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // تأكد من إعادة تعيين الكاش للصلاحيات والأدوار
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الصلاحيات المطلوبة
        $permissions = [
            'manage payments',
            'manage exam scores',
            'manage notifications',
            'view students',
            'view own data',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // تهيئة الأدوار
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // ربط الصلاحيات بالأدوار
        $admin->syncPermissions([
            'manage payments',
            'manage exam scores',
            'manage notifications',
            'view students',
            'view own data', // optional if admin also sees own data
        ]);

        $student->syncPermissions([
            'view own data',
        ]);

        // إعادة تعيين كاش بعد الانشاء
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

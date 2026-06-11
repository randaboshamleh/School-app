<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ابحث عن المستخدم المطلوب (عدّل الايميل حسب الحاجة)
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            $this->command->error('User not found!');

            return;
        }

        // ابحث عن الدور (role) المطلوب
        $role = Role::where('name', 'admin')->first();

        if (! $role) {
            $this->command->error('Role not found!');

            return;
        }

        // اربط المستخدم بالدور
        $user->assignRole($role->name);

        $this->command->info("Role '{$role->name}' assigned to user '{$user->email}' successfully.");
    }
}

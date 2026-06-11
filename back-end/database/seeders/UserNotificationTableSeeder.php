<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserNotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_notifications')->insert([
            [
                'user_id' => 85,
                'type' => 'App\Notifications\GeneralNotification',
                'title' => '📢 إشعار مهم',
                'body' => 'اليوم آخر موعد لتسديد الرسوم الدراسية',
                'data' => json_encode([]), // ممكن تخليه فارغ أو تحط بيانات إضافية
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 85,
                'type' => 'App\Notifications\GeneralNotification',
                'title' => '🎓 تنبيه أكاديمي',
                'body' => 'تمت إضافة علامات الامتحان النهائي',
                'data' => json_encode([]),
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

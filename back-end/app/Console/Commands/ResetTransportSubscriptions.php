<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\transport_subscribtions;
use Illuminate\Console\Command;

class ResetTransportSubscriptions extends Command
{
    protected $signature = 'transport:reset-subscriptions';

    protected $description = 'Reset transport subscriptions for the new academic year';

    public function handle()
    {
        $nowYear = now();
        $year = $nowYear->month >= 9 ? $nowYear->year.'-'.($nowYear->year + 1) : ($nowYear->year - 1).'-'.$nowYear->year;

        // 1) تأكد لكل طالب موجود سجل للسنة الجديدة (false by default)
        Student::chunk(200, function ($students) use ($year) {
            foreach ($students as $s) {
                \App\Models\transport_subscribtions::firstOrCreate(
                    ['student_id' => $s->id, 'academic_year' => $year],
                    ['is_subscribed' => false, 'route_id' => null]
                );
            }
        });

        // 2) لو بدك تضمن إلغاء كل الاشتراكات القديمة (بدون حذف السجلات)
        transport_subscribtions::where('academic_year', '<>', $year)
            ->update(['is_subscribed' => false, 'route_id' => null]);

        $this->info("Transport subscriptions reset for academic year: $year");
    }
}

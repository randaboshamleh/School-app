<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // جدولة المهام هنا

        $schedule->command('fcm:purge-old-tokens')->daily();

        $schedule->command('transport:reset-subscriptions')->dailyAt('00:05')->when(function () {
            $today = now()->toDateString(); // اليوم الحالي YYYY-MM-DD
            $semesterStart = \App\Models\AcademicYear::where('year', now()->year)
                ->value('semester_start');

            return $today === $semesterStart;
        });

    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

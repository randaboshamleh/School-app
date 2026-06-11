<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // استرجاع مجموعة من الصفوف والطلاب
        $classes = ClassRoom::all();
        $students = Student::all();

        // إنشاء سجلات حضور لأسبوع (مثال 5 أيام ماضية)
        foreach ($classes as $class) {
            foreach ($students as $student) {
                foreach (range(1, 5) as $dayOffset) {
                    $date = now()->subDays($dayOffset)->toDateString();

                    Attendance::firstOrCreate(
                        [
                            'class_room_id' => $class->id,
                            'student_id' => $student->id,
                            'date' => $date,
                        ],
                        [
                            'status' => ['present', 'absent', 'late'][array_rand([0, 1, 2])],
                            'time_in' => rand(0, 1) ? now()->setTime(rand(7, 8), rand(0, 59)) : null,
                            'time_out' => now()->setTime(rand(13, 15), rand(0, 59)), // يختار دقيقة من 0 إلى  59.يختار ساعة من 1 الى 3
                        ]
                    );
                }
            }
        }
    }
}

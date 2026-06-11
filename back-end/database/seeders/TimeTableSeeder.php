<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimeTable;
use Illuminate\Database\Seeder;

class TimeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = ClassRoom::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];

        foreach ($classes as $class) {
            foreach ($days as $day) {
                foreach (range(1, 6) as $periodOrder) { // عدد الحصص باليوم
                    $subject = $subjects->random();
                    $teacher = $teachers->random();
                    $startTime = now()->setTime(8 + $periodOrder, 0); // الساعة 8 صباحًا + رقم الحصة
                    $endTime = now()->setTime(8 + $periodOrder, 45); // بعد 45 دقيقة من وقت البدء

                    TimeTable::updateOrCreate(
                        [
                            'class_room_id' => $class->id,
                            'day_of_week' => $day,
                            'period_order' => $periodOrder,
                        ],
                        [
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'room' => 'شعبةا'.rand(1, 80),
                        ]
                    );
                }
            }
        }
    }
}

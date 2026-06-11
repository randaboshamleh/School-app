<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\ClassRoomsSubjectTeacher;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ClassRoomsSubjectTeacherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = ClassRoom::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        foreach ($classes as $class) {
            foreach ($subjects as $subject) {
                $teacher = $teachers->random(); // اختيار معلم عشوائي
                ClassRoomsSubjectTeacher::updateOrCreate(
                    [
                        'class_rooms_id' => $class->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'day_of_week' => collect(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'])->random(), // مثال ثابت أو اختياري
                    ],
                    [
                        'periods_per_week' => 30,
                        'minutes_per_period' => 45,
                        'room' => 'Room '.rand(1, 80),
                        'start_time' => now()->setTime(rand(8, 10), rand(0, 59))->format('H:i:s'),
                        'end_time' => now()->setTime(rand(8, 10), rand(45, 59))->format('H:i:s'),
                        'semester' => 'Fall',
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ExamsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = Subject::all();
        $classes = ClassRoom::all();

        if ($subjects->isEmpty() || $classes->isEmpty()) {
            $this->command->info('⚠️ أضف أولاً بيانات subjects و class_rooms للاستكمال.');

            return;
        }

        // 🔁 لكل مادة، أنشئ أكثر من امتحان (ميد + فاينل)
        foreach ($subjects as $subject) {
            $numExams = rand(1, 3); // عدد الامتحانات لكل مادة (عشوائي بين 1 و 3)

            for ($i = 1; $i <= $numExams; $i++) {
                // نختار فصل دراسي عشوائي
                $class = $classes->random();

                // نحدد تواريخ وأوقات عشوائية
                $examDate = now()->addDays(rand(3, 30))->toDateString();
                $startTime = now()->setTime(rand(8, 13), 0)->toTimeString();
                $endTime = now()->setTime(rand(14, 17), 0)->toTimeString();
                $duration = rand(60, 180); // مدة بين ساعة و3 ساعات

                Exam::updateOrCreate(
                    [
                        'exam_code' => 'EXAM-'.strtoupper($subject->id).'-'.$i,
                    ],
                    [
                        'title' => "امتحان {$subject->name} رقم $i",
                        'subject_id' => $subject->id, // ✅ نفس المادة
                        'classrooms_id' => $class->id,
                        'exam_date' => $examDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'duration_minutes' => $duration,
                        'instructions' => 'يرجى الحضور قبل الموعد بـ10 دقائق.',
                        'syllabus' => 'مطلوب من الصفحة'.rand(1, 20).'  إلى الصفحة'.rand(60, 200),
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ تم إنشاء الامتحانات بنجاح مع مواد حقيقية وأوقات عشوائية.');
    }
}

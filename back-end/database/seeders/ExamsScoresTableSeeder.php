<?php

namespace Database\Seeders;

use App\Models\exam_component;
use App\Models\ExamScore;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ExamsScoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examComponents = exam_component::all();

        Student::all()->each(function ($student) use ($examComponents) {
            foreach ($examComponents as $component) {
                $marks = rand(0, $component->max_marks ?? 100);

                ExamScore::updateOrCreate(
                    [
                        'exam_component_id' => $component->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'exam_id' => null, // ✅ نخليه فاضي
                        'marks_obtained' => $marks,
                        'passed' => $marks >= ($component->min_marks ?? 50),
                        'notes' => $marks < ($component->min_marks ?? 50) ? 'Needs improvement' : 'Good',
                    ]
                );
            }
        });
    }
}

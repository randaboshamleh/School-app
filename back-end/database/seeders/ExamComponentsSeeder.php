<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamComponentsSeeder extends Seeder
{
    public function run()
    {
        DB::table('exam_components')->insert([
            [
                'component_name' => 'المذاكرة',
                'max_marks' => 120,
                'min_marks' => 50,
                'subject_id' => 1,
                'exam_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_name' => 'الامتحان',
                'max_marks' => 400,
                'min_marks' => 150,
                'subject_id' => 1,
                'exam_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_name' => 'المذاكرة ',
                'max_marks' => 100,
                'min_marks' => 40,
                'subject_id' => 2,
                'exam_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_name' => 'الامتحان',
                'max_marks' => 300,
                'min_marks' => 120,
                'subject_id' => 2,
                'exam_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_name' => 'المذاكرة ',
                'max_marks' => 100,
                'min_marks' => 40,
                'subject_id' => 3,
                'exam_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_name' => 'الامتحان',
                'max_marks' => 300,
                'min_marks' => 120,
                'subject_id' => 3,
                'exam_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_name' => 'المذاكرة ',
                'max_marks' => 100,
                'min_marks' => 40,
                'subject_id' => 4,
                'exam_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'component_name' => 'الامتحان',
                'max_marks' => 300,
                'min_marks' => 120,
                'subject_id' => 4,
                'exam_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'رياضيات',
                'code' => 'MATH101',
                'description' => 'المادة الأساسية في الحساب والهندسة',
                'academic_year_id' => 1,
                'teacher_id' => 1,
                'is_mandatory' => true,
                'semester' => 'first',
            ],
            [
                'name' => 'فيزياء',
                'code' => 'PHYS101',
                'description' => 'مقدمة في الفيزياء العامة',
                'academic_year_id' => 1,
                'teacher_id' => 2,
                'is_mandatory' => true,
                'semester' => 'first',
            ],
            [
                'name' => 'كيمياء',
                'code' => 'CHEM101',
                'description' => 'أساسيات الكيمياء وتحليل المواد',
                'academic_year_id' => 1,
                'teacher_id' => 3,
                'is_mandatory' => true,
                'semester' => 'second',
            ],
            [
                'name' => 'لغة انجليزية',
                'code' => 'ENG101',
                'description' => 'تطوير مهارات القراءة والكتابة بالإنجليزية',
                'academic_year_id' => 1,
                'teacher_id' => 6,
                'is_mandatory' => true,
                'semester' => 'second',
            ],
        ];

        foreach ($subjects as $data) {
            Subject::updateOrCreate(
                ['code' => $data['code']], // المفتاح الفريد للمادة
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'academic_year_id' => $data['academic_year_id'],
                    'teacher_id' => $data['teacher_id'],
                    'is_mandatory' => $data['is_mandatory'],
                    'semester' => $data['semester'],
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentsTableSeeder extends Seeder
{
    public function run(): void
    {

        $currentYear = AcademicYear::where('is_current', true)->first();
        if (! $currentYear) {
            $this->command->error('No current academic year found.');

            return;
            Enrollment::create([
                'student_id' => 1001,
                'class_room_id' => 2,
                'academic_year_id' => 1,
                'enrolled_at' => now(),
                'status' => 'active',
                'is_current' => true,
                'fees_paid' => 500.00,
                'notes' => 'دفعة أولى',
            ]);
        }

        $students = Student::all();
        $classes = ClassRoom::where('academic_year_id', $currentYear->id)->get();

        foreach ($students as $student) {
            foreach ($classes as $class) {
                Enrollment::firstOrCreate(
                    [
                        'student_id' => $student->student_id,
                        'class_room_id' => $class->id,
                        'academic_year_id' => $currentYear->id,
                    ],
                    [
                        'enrolled_at' => now()->subMonths(rand(1, 5)),
                        'status' => 'active',
                        'is_current' => true,
                        'fees_paid' => rand(1000000, 20000000), // مثال: 1,000,000 – 20,000,000
                        'notes' => null,
                    ]
                );
            }
        }
    }
}

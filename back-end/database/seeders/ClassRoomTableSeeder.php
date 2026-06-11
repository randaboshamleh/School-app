<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\classRoom;
use Illuminate\Database\Seeder;

class ClassRoomTableSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::where('is_current', true)->first();
        if (! $year) {
            return;
        }

        $classNames = [
            'الصف الأول شعبة أ',
            'الصف الثاني شعبة أ',
            'الصف الثالث شعبة أ',
            'الصف الرايع شعبة أ',
            'الصف الخامس شعبة أ',
            'الصف السادس شعبة أ',
            'الصف السابع شعبة أ',
            'الصف الثامن شعبة أ',
            'الصف التاسع شعبة أ',
            'الصف العاشر شعبة أ',
            'الصف الحادي عشر شعبة أ',
            'الصف الثاني عشر شعبة أ',
        ];

        foreach ($classNames as $name) {
            ClassRoom::updateOrCreate(
                ['name' => $name, 'academic_year_id' => $year->id],
                [
                    'capacity' => 30,
                    'location' => 'الطابق الاول',
                    'is_active' => true,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicYear::where('is_current', true)->update(['is_current' => false]);
        AcademicYear::firstOrCreate(['year' => '2025-2026'],
            [
                'start_date' => '2025-09-01',
                'end_date' => '2026-06-30',
                'term_start_dates' => json_encode(['2025-09-15', '2026-01-15']),
                'is_current' => true,
            ]);

    }
}

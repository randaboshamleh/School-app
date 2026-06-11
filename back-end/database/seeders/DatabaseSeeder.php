<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserTableSeeder::class,
            TimeTableSeeder::class,
            AcademicYearsTableSeeder::class,
            TeacherTableSeeder::class,
            SubjectTableSeeder::class,
            StudentTableSeeder::class,
            RolesTableSeeder::class,
            PaymentTableSeeder::class,
            UserNotificationTableSeeder::class,
            ExamsTableSeeder::class,
            ExamsScoresTableSeeder::class,
            EnrollmentsTableSeeder::class,
            ClassRoomTableSeeder::class,
            ClassRoomsSubjectTeacherTableSeeder::class,
            AttendancesTableSeeder::class,
            AdminRoleTableSeeder::class,
            TransportRoutesSeeder::class,
            ExamComponentsSeeder::class,
        ]);
    }
}

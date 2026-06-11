<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class StudentWorkflowTest extends TestCase
{
    private bool $transactionStarted = false;

    protected function setUp(): void
    {
        parent::setUp();

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database connection is not available: '.$e->getMessage());
        }

        DB::beginTransaction();
        $this->transactionStarted = true;
    }

    protected function tearDown(): void
    {
        if ($this->transactionStarted) {
            DB::rollBack();
            $this->transactionStarted = false;
        }

        parent::tearDown();
    }

    public function test_student_can_login_and_use_token_to_fetch_current_user(): void
    {
        $student = $this->createStudentUser();

        $loginResponse = $this->postJson('/api/login', [
            'student_id' => $student->student_id,
            'password' => 'secret123',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'student_id',
                    'role',
                ],
            ]);

        $token = $loginResponse->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/user')
            ->assertOk()
            ->assertJson([
                'id' => $student->id,
                'student_id' => $student->student_id,
            ]);
    }

    public function test_student_can_fetch_exam_scores(): void
    {
        $student = $this->createStudentUser();
        $classRoomId = $this->createClassRoom();
        $subjectId = $this->createSubject();
        $componentId = $this->createExamComponent($subjectId, $classRoomId);

        $this->insertForExistingColumns('exam_scores', [
            'student_id' => $student->student_id,
            'exam_component_id' => $componentId,
            'marks_obtained' => 88,
            'passed' => true,
            'notes' => 'workflow test score',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($student, ['*']);

        $this->getJson('/api/exam-scores')
            ->assertOk()
            ->assertJsonPath('student', $student->name)
            ->assertJsonFragment([
                'marks_obtained' => 88,
            ]);
    }

    public function test_student_can_fetch_payments(): void
    {
        $student = $this->createStudentUser();
        $enrollmentId = $this->createEnrollment($student->student_id);

        $paymentId = $this->insertForExistingColumns('payments', [
            'student_id' => $student->student_id,
            'enrollment_id' => $enrollmentId,
            'amount' => 450,
            'method' => 'cash',
            'reference' => 'WF-PAY-'.uniqid(),
            'due_date' => now()->addDays(7),
            'status' => 'pending',
            'is_paid' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($student, ['*']);

        $this->getJson('/api/payments')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $paymentId,
                'student_id' => $student->student_id,
                'status' => 'pending',
            ]);
    }

    public function test_student_can_subscribe_to_transport_and_cancel_subscription(): void
    {
        $student = $this->createStudentUser();
        $this->createEnrollment($student->student_id);
        $routeId = $this->createTransportRoute();

        Sanctum::actingAs($student, ['*']);

        $this->getJson('/api/transport/status')
            ->assertOk()
            ->assertJson([
                'status' => false,
            ]);

        $subscribeResponse = $this->postJson('/api/transport/subscribe', [
            'route_id' => $routeId,
        ]);

        $this->assertSame(200, $subscribeResponse->status(), $subscribeResponse->getContent());

        $subscribeResponse->assertJsonStructure([
                'message',
                'subscription',
                'payment',
            ]);

        $this->getJson('/api/transport/status')
            ->assertOk()
            ->assertJson([
                'status' => true,
                'route_id' => $routeId,
            ]);

        $this->deleteJson("/api/transport/subscribe/{$routeId}")
            ->assertOk()
            ->assertJsonStructure([
                'message',
            ]);

        $this->getJson('/api/transport/status')
            ->assertOk()
            ->assertJson([
                'status' => false,
                'route_id' => $routeId,
            ]);
    }

    private function createStudentUser(): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'sanctum',
        ]);

        $studentId = random_int(900000, 999999);

        $user = User::create([
            'name' => 'Workflow Student '.$studentId,
            'email' => "workflow-student-{$studentId}@example.test",
            'student_id' => $studentId,
            'role' => 'student',
            'password' => Hash::make('secret123'),
        ]);

        $user->assignRole('student');

        $this->insertForExistingColumns('students', [
            'student_id' => $studentId,
            'user_id' => $user->id,
            'name' => $user->name,
            'password' => Hash::make('secret123'),
            'address' => 'Test address',
            'parent_contact' => '0000000000',
            'profile_photo' => 'test.png',
            'enrollment_date' => now()->toDateString(),
            'gender' => 'male',
            'status' => 'active',
            'nationality' => 'Test',
            'class' => '1',
            'section' => 'A',
            'gpa' => 3.5,
            'stage' => 'primary',
            'grade_level' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $user;
    }

    private function currentAcademicYearId(): int
    {
        $year = now()->month >= 9 ? now()->year : now()->year - 1;
        $yearLabel = $year.'-'.($year + 1);

        $existing = DB::table('academic_years')
            ->where('is_current', true)
            ->orWhere('year', (string) $year)
            ->orWhere('year', $yearLabel)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        return $this->insertForExistingColumns('academic_years', [
            'year' => (string) $year,
            'start_date' => "{$year}-09-01",
            'end_date' => ($year + 1).'-06-30',
            'num_terms' => 2,
            'term_start_dates' => json_encode(["{$year}-09-01", ($year + 1).'-02-01']),
            'is_current' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createClassRoom(): int
    {
        return $this->insertForExistingColumns($this->classRoomTable(), [
            'name' => 'Workflow Class '.uniqid(),
            'academic_year_id' => $this->currentAcademicYearId(),
            'capacity' => 30,
            'location' => 'Test building',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSubject(): int
    {
        return $this->insertForExistingColumns('subjects', [
            'name' => 'Workflow Subject '.uniqid(),
            'code' => 'WF-'.uniqid(),
            'description' => 'Subject created by workflow test',
            'weekly_session' => 3,
            'weekly_sessions' => 3,
            'academic_year_id' => $this->currentAcademicYearId(),
            'teacher_id' => $this->createTeacher(),
            'is_mandatory' => true,
            'semester' => 'first',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createExamComponent(int $subjectId, int $classRoomId): int
    {
        return $this->insertForExistingColumns('exam_components', [
            'exam_id' => $this->createExam($subjectId, $classRoomId),
            'subject_id' => $subjectId,
            'class_room_id' => $classRoomId,
            'classrooms_id' => $classRoomId,
            'component_name' => 'Final',
            'max_marks' => 100,
            'min_marks' => 50,
            'weight_percentage' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createEnrollment(int $studentId): int
    {
        $classRoomId = $this->createClassRoom();

        return $this->insertForExistingColumns('enrollments', [
            'student_id' => $studentId,
            'class_room_id' => $classRoomId,
            'classrooms_id' => $classRoomId,
            'academic_year_id' => $this->currentAcademicYearId(),
            'enrolled_at' => now()->toDateString(),
            'scholarship_percentage' => 0,
            'tuition_fee' => 1000,
            'status' => 'active',
            'is_current' => true,
            'fees_paid' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createExam(int $subjectId, int $classRoomId): int
    {
        return $this->insertForExistingColumns('exams', [
            'title' => 'Workflow Exam '.uniqid(),
            'exam_code' => 'WF-EXAM-'.uniqid(),
            'subject_id' => $subjectId,
            'class_room_id' => $classRoomId,
            'classrooms_id' => $classRoomId,
            'exam_date' => now()->addWeek()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'duration_minutes' => 60,
            'instructions' => 'Workflow test exam',
            'syllabus' => 'Chapter 1',
            'is_active' => true,
            'exam_type' => 'final',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createTransportRoute(): int
    {
        return $this->insertForExistingColumns('transport_routes', [
            'name' => 'Workflow Route '.uniqid(),
            'code' => 'WF-ROUTE-'.uniqid(),
            'active' => true,
            'price' => 125,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createTeacher(): int
    {
        return $this->insertForExistingColumns('teachers', [
            'first_name' => 'Workflow',
            'last_name' => 'Teacher',
            'phone_number' => '0000000000',
            'gender' => 'male',
            'specialization' => 'Testing',
            'hire_date' => now()->toDateString(),
            'address' => 'Test address',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function classRoomTable(): string
    {
        if (Schema::hasTable('class_rooms')) {
            return 'class_rooms';
        }

        if (Schema::hasTable('classrooms')) {
            return 'classrooms';
        }

        $this->fail('Neither class_rooms nor classrooms table exists.');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function insertForExistingColumns(string $table, array $payload): int
    {
        $columns = Schema::getColumnListing($table);
        $data = array_intersect_key($payload, array_flip($columns));

        return (int) DB::table($table)->insertGetId($data);
    }
}

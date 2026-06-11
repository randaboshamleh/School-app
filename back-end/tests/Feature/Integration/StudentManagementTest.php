<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class StudentManagementTest extends TestCase
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
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function tearDown(): void
    {
        if ($this->transactionStarted) {
            DB::rollBack();
            $this->transactionStarted = false;
        }

        parent::tearDown();
    }

    public function test_admin_can_manage_students(): void
    {
        Sanctum::actingAs($this->createUserWithRole('admin'), ['*']);

        $studentId = random_int(800000, 899999);

        $createResponse = $this->postJson('/api/students', [
            'student_id' => $studentId,
            'name' => 'Managed Student',
            'email' => "managed-{$studentId}@example.test",
            'password' => 'secret123',
            'class' => '4',
            'section' => 'B',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Test address',
            'parent_contact' => '0000000000',
            'nationality' => 'Test',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonFragment([
                'student_id' => $studentId,
                'name' => 'Managed Student',
            ]);

        $this->getJson('/api/students')
            ->assertOk()
            ->assertJsonFragment([
                'student_id' => $studentId,
            ]);

        $this->putJson("/api/students/{$studentId}", [
            'name' => 'Updated Managed Student',
            'section' => 'C',
            'status' => 'suspended',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Updated Managed Student',
                'section' => 'C',
                'status' => 'suspended',
            ]);

        $this->deleteJson("/api/students/{$studentId}")
            ->assertOk()
            ->assertJson([
                'message' => 'Student deleted successfully',
            ]);
    }

    public function test_student_cannot_manage_all_students(): void
    {
        Sanctum::actingAs($this->createUserWithRole('student'), ['*']);

        $this->getJson('/api/students')->assertForbidden();

        $this->postJson('/api/students', [
            'student_id' => random_int(700000, 799999),
            'name' => 'Forbidden Student',
        ])->assertForbidden();
    }

    public function test_supervisor_only_manages_students_in_assigned_stage_and_gender(): void
    {
        $visible = $this->createManagedStudent('primary', 'female');
        $hidden = $this->createManagedStudent('secondary', 'female');

        Sanctum::actingAs($this->createUserWithRole('primary_female_supervisor'), ['*']);

        $this->getJson('/api/students')
            ->assertOk()
            ->assertJsonFragment(['student_id' => $visible])
            ->assertJsonMissing(['student_id' => $hidden]);

        $this->putJson("/api/students/{$hidden}", [
            'name' => 'Should Not Update',
        ])->assertForbidden();

        $createdId = random_int(500000, 599999);

        $this->postJson('/api/students', [
            'student_id' => $createdId,
            'name' => 'Scoped Student',
            'email' => "scoped-{$createdId}@example.test",
            'password' => 'secret123',
            'stage' => 'secondary',
            'gender' => 'male',
        ])
            ->assertCreated()
            ->assertJsonFragment([
                'student_id' => $createdId,
                'stage' => 'primary',
                'gender' => 'female',
            ]);
    }

    private function createUserWithRole(string $roleName): User
    {
        Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'sanctum',
        ]);

        $studentId = random_int(600000, 699999);

        $user = User::create([
            'name' => ucfirst($roleName).' User '.$studentId,
            'email' => "{$roleName}-{$studentId}@example.test",
            'student_id' => $studentId,
            'role' => $roleName,
            'password' => Hash::make('secret123'),
        ]);

        $user->assignRole($roleName);

        return $user;
    }

    private function createManagedStudent(string $stage, string $gender): int
    {
        Sanctum::actingAs($this->createUserWithRole('admin'), ['*']);

        $studentId = random_int(300000, 499999);

        $this->postJson('/api/students', [
            'student_id' => $studentId,
            'name' => "Managed {$stage} {$gender}",
            'email' => "managed-{$studentId}@example.test",
            'password' => 'secret123',
            'class' => '1',
            'section' => 'A',
            'stage' => $stage,
            'gender' => $gender,
            'status' => 'active',
        ])->assertCreated();

        return $studentId;
    }
}

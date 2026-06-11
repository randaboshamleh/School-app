<?php

namespace Tests\Feature\Integration;

use App\Models\Advertisements;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdvertisementManagementTest extends TestCase
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

    public function test_admin_can_create_update_and_delete_advertisements(): void
    {
        Sanctum::actingAs($this->createUserWithRole('admin'), ['*']);

        $create = $this->postJson('/api/ads', [
            'content' => 'Admin announcement',
        ]);

        $create
            ->assertCreated()
            ->assertJsonFragment(['content' => 'Admin announcement']);

        $adId = $create->json('id');

        $this->putJson("/api/ads/{$adId}", [
            'content' => 'Updated admin announcement',
        ])
            ->assertOk()
            ->assertJsonPath('ad.content', 'Updated admin announcement');

        $this->deleteJson("/api/ads/{$adId}")
            ->assertOk();
    }

    public function test_supervisor_cannot_manage_advertisements(): void
    {
        Sanctum::actingAs($this->createUserWithRole('primary_female_supervisor'), ['*']);

        $ad = Advertisements::create(['content' => 'Existing announcement']);

        $this->postJson('/api/ads', ['content' => 'Forbidden'])->assertForbidden();
        $this->putJson("/api/ads/{$ad->id}", ['content' => 'Forbidden'])->assertForbidden();
        $this->deleteJson("/api/ads/{$ad->id}")->assertForbidden();
    }

    private function createUserWithRole(string $roleName): User
    {
        Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'sanctum',
        ]);

        $studentId = random_int(200000, 299999);

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
}

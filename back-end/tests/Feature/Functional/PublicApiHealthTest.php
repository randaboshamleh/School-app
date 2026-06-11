<?php

namespace Tests\Feature\Functional;

use Tests\TestCase;

class PublicApiHealthTest extends TestCase
{
    public function test_web_root_returns_health_payload(): void
    {
        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
            ])
            ->assertJsonStructure([
                'name',
                'status',
            ]);
    }

    public function test_api_ping_returns_ok(): void
    {
        $this->getJson('/api/ping')
            ->assertOk()
            ->assertExactJson([
                'status' => 'ok',
            ]);
    }

    public function test_api_test_endpoint_returns_expected_message(): void
    {
        $this->getJson('/api/test')
            ->assertOk()
            ->assertJson([
                'message' => 'API is working!',
            ]);
    }

    public function test_login_validation_rejects_missing_credentials(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'student_id',
                'password',
            ]);
    }

    public function test_register_validation_rejects_missing_payload(): void
    {
        $this->postJson('/api/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'student_id',
                'password',
                'password_confirmation',
            ]);
    }
}

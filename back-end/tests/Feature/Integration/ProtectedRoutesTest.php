<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function protectedRoutesProvider(): array
    {
        return [
            'current user profile' => ['GET', '/api/user'],
            'whoami diagnostics' => ['GET', '/api/whoami'],
            'student exam scores' => ['GET', '/api/exam-scores'],
            'transport status' => ['GET', '/api/transport/status'],
            'supervisor dashboard' => ['GET', '/api/supervisor/dashboard'],
            'admin users index' => ['GET', '/api/users'],
            'admin roles index' => ['GET', '/api/roles'],
        ];
    }

    /**
     * @dataProvider protectedRoutesProvider
     */
    public function test_protected_routes_require_authentication(string $method, string $uri): void
    {
        $this->json($method, $uri)
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_route_contracts_are_registered(): void
    {
        $expectedRoutes = [
            'GET /',
            'GET api/ping',
            'GET api/test',
            'POST api/login',
            'POST api/register',
            'GET api/user',
            'GET api/transport/status',
            'POST api/supervisor/login',
        ];

        $registered = collect(app('router')->getRoutes())->map(
            fn ($route) => implode('|', $route->methods()).' '.$route->uri()
        );

        foreach ($expectedRoutes as $route) {
            [$method, $uri] = explode(' ', $route, 2);

            $this->assertTrue(
                $registered->contains(fn ($registeredRoute) => str_contains($registeredRoute, $method) && str_ends_with($registeredRoute, $uri)),
                "Expected route [{$route}] to be registered."
            );
        }
    }
}

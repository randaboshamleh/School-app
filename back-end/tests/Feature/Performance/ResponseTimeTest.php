<?php

namespace Tests\Feature\Performance;

use Tests\TestCase;

class ResponseTimeTest extends TestCase
{
    public function test_lightweight_health_endpoints_respond_quickly(): void
    {
        $endpoints = [
            '/',
            '/api/ping',
            '/api/test',
        ];

        $durations = [];

        foreach ($endpoints as $endpoint) {
            for ($i = 0; $i < 5; $i++) {
                $startedAt = hrtime(true);

                $this->getJson($endpoint)->assertOk();

                $durations[] = (hrtime(true) - $startedAt) / 1_000_000;
            }
        }

        $averageMs = array_sum($durations) / count($durations);
        $maxMs = max($durations);

        $this->assertLessThan(250, $averageMs, "Average response time was {$averageMs}ms.");
        $this->assertLessThan(1000, $maxMs, "Slowest response time was {$maxMs}ms.");
    }
}

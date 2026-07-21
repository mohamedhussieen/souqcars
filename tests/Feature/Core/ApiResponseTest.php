<?php

namespace Tests\Feature\Core;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/** Verifies the unified API response envelope shape across success, pagination, validation, auth, and rate-limit cases. */
class ApiResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_response_has_the_unified_shape(): void
    {
        $response = $this->getJson('/api/v1/mobile/lookup/colors');

        $response->assertJsonStructure(['success', 'message', 'data', 'meta', 'errors']);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('errors', null);
    }

    public function test_paginated_response_has_the_expected_meta_shape(): void
    {
        $response = $this->getJson('/api/v1/mobile/lookup/cities');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
    }

    public function test_validation_error_returns_422_with_errors(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/login', []);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonStructure(['errors']);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/v1/mobile/profile');

        $response->assertStatus(401);
        $response->assertJsonPath('success', false);
    }

    public function test_rate_limited_request_returns_429_with_retry_after(): void
    {
        RateLimiter::clear('6,1:' . request()->ip());

        $email = 'ratelimit-test@example.com';
        $lastResponse = null;

        for ($i = 0; $i < 7; $i++) {
            $lastResponse = $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => $email]);
        }

        $lastResponse->assertStatus(429);
        $lastResponse->assertHeader('Retry-After');
        $lastResponse->assertJsonPath('success', false);
    }
}

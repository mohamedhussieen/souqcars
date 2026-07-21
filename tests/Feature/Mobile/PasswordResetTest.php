<?php

namespace Tests\Feature\Mobile;

use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** Verifies the mobile forgot-password flow: send OTP, verify OTP, reset password. */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_forgot_password_responds_success_for_existing_user(): void
    {
        User::factory()->create(['email' => 'a@b.com']);

        $response = $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);

        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertDatabaseHas('password_reset_otps', ['email' => 'a@b.com']);
    }

    public function test_forgot_password_responds_success_for_unknown_email_without_leaking_existence(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'nobody@example.com']);

        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertDatabaseMissing('password_reset_otps', ['email' => 'nobody@example.com']);
    }

    public function test_verify_reset_otp_returns_a_reset_token(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);

        $response = $this->postJson('/api/v1/mobile/auth/verify-reset-otp', ['email' => 'a@b.com', 'otp' => '1234']);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['reset_token']]);
    }

    public function test_verify_reset_otp_fails_with_wrong_code(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);

        $response = $this->postJson('/api/v1/mobile/auth/verify-reset-otp', ['email' => 'a@b.com', 'otp' => '0000']);

        $response->assertStatus(400)->assertJsonPath('success', false);
    }

    public function test_reset_password_updates_the_password_with_a_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'a@b.com']);
        $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);
        $verifyResponse = $this->postJson('/api/v1/mobile/auth/verify-reset-otp', ['email' => 'a@b.com', 'otp' => '1234']);
        $resetToken = $verifyResponse->json('data.reset_token');

        $response = $this->postJson('/api/v1/mobile/auth/reset-password', [
            'email'                 => 'a@b.com',
            'reset_token'           => $resetToken,
            'password'              => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);
        $this->postJson('/api/v1/mobile/auth/verify-reset-otp', ['email' => 'a@b.com', 'otp' => '1234']);

        $response = $this->postJson('/api/v1/mobile/auth/reset-password', [
            'email'                 => 'a@b.com',
            'reset_token'           => 'invalid-token',
            'password'              => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(400)->assertJsonPath('success', false);
    }

    public function test_reset_password_validation_requires_matching_confirmation(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/reset-password', [
            'email'                 => 'a@b.com',
            'reset_token'           => 'token',
            'password'              => 'NewPassword123',
            'password_confirmation' => 'Mismatch123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public function test_forgot_password_is_throttled_after_six_requests_per_minute(): void
    {
        User::factory()->create(['email' => 'a@b.com']);

        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com'])
                ->assertStatus(200);
        }

        $response = $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);

        $response->assertStatus(429)->assertJsonPath('success', false);
    }

    public function test_verify_reset_otp_is_throttled_after_six_requests_per_minute(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);

        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/v1/mobile/auth/verify-reset-otp', ['email' => 'a@b.com', 'otp' => '0000']);
        }

        $response = $this->postJson('/api/v1/mobile/auth/verify-reset-otp', ['email' => 'a@b.com', 'otp' => '1234']);

        $response->assertStatus(429);
    }

    public function test_verify_reset_otp_locks_out_after_max_attempts(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->postJson('/api/v1/mobile/auth/forgot-password', ['email' => 'a@b.com']);

        for ($i = 0; $i < PasswordResetOtp::MAX_ATTEMPTS; $i++) {
            $this->postJson('/api/v1/mobile/auth/verify-reset-otp', ['email' => 'a@b.com', 'otp' => '0000']);
        }

        $record = PasswordResetOtp::where('email', 'a@b.com')->first();
        $this->assertGreaterThanOrEqual(PasswordResetOtp::MAX_ATTEMPTS, $record->attempts);
    }
}

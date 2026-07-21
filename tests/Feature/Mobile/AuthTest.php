<?php

namespace Tests\Feature\Mobile;

use App\Models\OtpCode;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** Verifies the mobile auth endpoints: register, login, OTP send/verify, logout. */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        Mail::fake();
    }

    public function test_register_creates_a_user_and_returns_a_token(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'phone'                 => '01012345678',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'test@example.com')
            ->assertJsonStructure(['data' => ['user', 'token']]);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_fails_with_mismatched_password_confirmation(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'phone'                 => '01012345678',
            'password'              => 'Password123',
            'password_confirmation' => 'Different123',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/mobile/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'phone'                 => '01012345678',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_fails_with_duplicate_phone(): void
    {
        User::factory()->create(['phone' => '01012345678']);

        $response = $this->postJson('/api/v1/mobile/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'unique@example.com',
            'phone'                 => '01012345678',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['phone']);
    }

    public function test_register_allows_reusing_email_and_phone_freed_by_self_delete_account(): void
    {
        $deleted = User::factory()->create([
            'email'              => 'reused@example.com',
            'phone'              => '01055555555',
            'policy_accepted_at' => now(),
        ]);
        $token = $deleted->createToken('mobile-app')->plainTextToken;
        $this->withHeader('Authorization', "Bearer {$token}")->deleteJson('/api/v1/mobile/profile')
            ->assertStatus(200);

        $response = $this->postJson('/api/v1/mobile/auth/register', [
            'name'                  => 'New Owner',
            'email'                 => 'reused@example.com',
            'phone'                 => '01055555555',
            'password'              => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'reused@example.com', 'name' => 'New Owner']);
    }

    public function test_register_validation_requires_a_minimum_password_length(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'phone'                 => '01012345678',
            'password'              => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => 'Password123']);

        $response = $this->postJson('/api/v1/mobile/auth/login', [
            'email'    => 'a@b.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => 'Password123']);

        $response = $this->postJson('/api/v1/mobile/auth/login', [
            'email'    => 'a@b.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)->assertJsonPath('success', false);
    }

    public function test_login_fails_for_inactive_account(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => 'Password123', 'is_active' => false]);

        $response = $this->postJson('/api/v1/mobile/auth/login', [
            'email'    => 'a@b.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(403)->assertJsonPath('success', false);
    }

    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_otp_send_creates_a_code_and_returns_success(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/otp/send', ['email' => 'a@b.com']);

        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertDatabaseHas('otp_codes', ['email' => 'a@b.com']);
    }

    public function test_otp_send_is_throttled_on_resend(): void
    {
        $this->postJson('/api/v1/mobile/auth/otp/send', ['email' => 'a@b.com']);

        $response = $this->postJson('/api/v1/mobile/auth/otp/send', ['email' => 'a@b.com']);

        $response->assertStatus(429)->assertJsonPath('success', false);
    }

    public function test_otp_verify_succeeds_with_correct_code(): void
    {
        OtpCode::create(['email' => 'a@b.com', 'code' => '1234', 'expires_at' => now()->addMinutes(5)]);

        $response = $this->postJson('/api/v1/mobile/auth/otp/verify', ['email' => 'a@b.com', 'otp_code' => '1234']);

        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_otp_verify_fails_with_wrong_code(): void
    {
        OtpCode::create(['email' => 'a@b.com', 'code' => '1234', 'expires_at' => now()->addMinutes(5)]);

        $response = $this->postJson('/api/v1/mobile/auth/otp/verify', ['email' => 'a@b.com', 'otp_code' => '9999']);

        $response->assertStatus(400)->assertJsonPath('success', false);
    }

    public function test_otp_verify_validation_requires_four_digit_code(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/otp/verify', ['email' => 'a@b.com', 'otp_code' => '12']);

        $response->assertStatus(422)->assertJsonValidationErrors(['otp_code']);
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile-app')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/mobile/auth/logout');

        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_fails_without_a_token(): void
    {
        $response = $this->postJson('/api/v1/mobile/auth/logout');

        $response->assertStatus(401);
    }
}

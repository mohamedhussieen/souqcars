<?php

namespace Tests\Unit;

use App\Models\OtpCode;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Services\AuthService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** Verifies AuthService registration, login, OTP send/verify throttling, and logout. */
class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->service = new AuthService();
        Mail::fake();
    }

    private function registrationData(): array
    {
        return [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'phone'    => '01012345678',
            'password' => 'Password123',
        ];
    }

    public function test_register_creates_a_user_with_default_role_and_token(): void
    {
        $result = $this->service->register($this->registrationData());

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertTrue($result['user']->hasRole('user'));
        $this->assertNotEmpty($result['token']);
    }

    public function test_register_stores_fcm_token_when_provided(): void
    {
        $data = $this->registrationData();
        $data['fcm_token'] = 'device-token-123';

        $result = $this->service->register($data);

        $this->assertDatabaseHas('user_fcm_tokens', [
            'user_id' => $result['user']->id,
            'token'   => 'device-token-123',
        ]);
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        $user = User::factory()->create(['email' => 'a@b.com', 'password' => 'Password123']);

        $result = $this->service->login('a@b.com', 'Password123');

        $this->assertNotNull($result);
        $this->assertTrue($result['user']->is($user));
        $this->assertNotEmpty($result['token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => 'Password123']);

        $result = $this->service->login('a@b.com', 'WrongPassword');

        $this->assertNull($result);
    }

    public function test_login_fails_for_unknown_email(): void
    {
        $result = $this->service->login('nobody@example.com', 'Password123');

        $this->assertNull($result);
    }

    public function test_login_returns_inactive_flag_for_deactivated_user(): void
    {
        User::factory()->create(['email' => 'a@b.com', 'password' => 'Password123', 'is_active' => false]);

        $result = $this->service->login('a@b.com', 'Password123');

        $this->assertSame(['inactive' => true], $result);
    }

    public function test_login_reassigns_fcm_token_from_another_user(): void
    {
        $previousOwner = User::factory()->create();
        UserFcmToken::create(['user_id' => $previousOwner->id, 'token' => 'shared-token']);
        $user = User::factory()->create(['email' => 'a@b.com', 'password' => 'Password123']);

        $this->service->login('a@b.com', 'Password123', 'shared-token');

        $this->assertDatabaseHas('user_fcm_tokens', ['user_id' => $user->id, 'token' => 'shared-token']);
        $this->assertDatabaseCount('user_fcm_tokens', 1);
    }

    public function test_send_otp_creates_a_code_and_sends_mail(): void
    {
        $cooldown = $this->service->sendOtp('otp@example.com');

        $this->assertNull($cooldown);
        $this->assertDatabaseHas('otp_codes', ['email' => 'otp@example.com', 'code' => '1234']);
        Mail::assertSent(\App\Mail\OtpCodeMail::class);
    }

    public function test_send_otp_is_throttled_within_cooldown_window(): void
    {
        $this->service->sendOtp('otp@example.com');

        $cooldown = $this->service->sendOtp('otp@example.com');

        $this->assertNotNull($cooldown);
        $this->assertGreaterThan(0, $cooldown);
        $this->assertDatabaseCount('otp_codes', 1);
    }

    public function test_send_otp_allows_resend_after_cooldown_expires(): void
    {
        $otp = OtpCode::create([
            'email'      => 'otp@example.com',
            'code'       => '1234',
            'expires_at' => now()->addMinutes(15),
        ]);
        $otp->timestamps = false;
        $otp->created_at = now()->subMinutes(10);
        $otp->save();

        $cooldown = $this->service->sendOtp('otp@example.com');

        $this->assertNull($cooldown);
    }

    public function test_verify_otp_succeeds_with_valid_code(): void
    {
        OtpCode::create(['email' => 'otp@example.com', 'code' => '1234', 'expires_at' => now()->addMinutes(5)]);

        $result = $this->service->verifyOtp('otp@example.com', '1234');

        $this->assertTrue($result);
        $this->assertDatabaseHas('otp_codes', ['email' => 'otp@example.com', 'used' => true]);
    }

    public function test_verify_otp_fails_with_wrong_code(): void
    {
        OtpCode::create(['email' => 'otp@example.com', 'code' => '1234', 'expires_at' => now()->addMinutes(5)]);

        $result = $this->service->verifyOtp('otp@example.com', '9999');

        $this->assertFalse($result);
    }

    public function test_verify_otp_fails_when_expired(): void
    {
        OtpCode::create(['email' => 'otp@example.com', 'code' => '1234', 'expires_at' => now()->subMinute()]);

        $result = $this->service->verifyOtp('otp@example.com', '1234');

        $this->assertFalse($result);
    }

    public function test_verify_otp_fails_when_already_used(): void
    {
        OtpCode::create(['email' => 'otp@example.com', 'code' => '1234', 'expires_at' => now()->addMinutes(5), 'used' => true]);

        $result = $this->service->verifyOtp('otp@example.com', '1234');

        $this->assertFalse($result);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile-app');
        $user->withAccessToken($token->accessToken);

        $this->service->logout($user);

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->accessToken->id]);
    }
}

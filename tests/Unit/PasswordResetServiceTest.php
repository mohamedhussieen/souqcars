<?php

namespace Tests\Unit;

use App\Exceptions\PasswordResetException;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** Verifies PasswordResetService OTP issuance, verification, attempt lockout, and password reset. */
class PasswordResetServiceTest extends TestCase
{
    use RefreshDatabase;

    private PasswordResetService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PasswordResetService();
        Mail::fake();
    }

    public function test_send_otp_creates_a_record_for_existing_user(): void
    {
        User::factory()->create(['email' => 'a@b.com']);

        $this->service->sendOtp('a@b.com');

        $this->assertDatabaseHas('password_reset_otps', ['email' => 'a@b.com']);
        Mail::assertSent(\App\Mail\PasswordResetOtpMail::class);
    }

    public function test_send_otp_does_nothing_for_unknown_email(): void
    {
        $this->service->sendOtp('nobody@example.com');

        $this->assertDatabaseMissing('password_reset_otps', ['email' => 'nobody@example.com']);
        Mail::assertNotSent(\App\Mail\PasswordResetOtpMail::class);
    }

    public function test_send_otp_replaces_any_previous_record_for_the_same_email(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->service->sendOtp('a@b.com');

        $this->service->sendOtp('a@b.com');

        $this->assertDatabaseCount('password_reset_otps', 1);
    }

    public function test_verify_otp_returns_a_reset_token_for_the_correct_code(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->service->sendOtp('a@b.com');

        $token = $this->service->verifyOtp('a@b.com', '1234');

        $this->assertNotEmpty($token);
        $record = PasswordResetOtp::where('email', 'a@b.com')->first();
        $this->assertTrue(Hash::check($token, $record->reset_token));
    }

    public function test_verify_otp_throws_when_no_record_exists(): void
    {
        $this->expectException(PasswordResetException::class);

        $this->service->verifyOtp('nobody@example.com', '1234');
    }

    public function test_verify_otp_throws_and_increments_attempts_on_wrong_code(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->service->sendOtp('a@b.com');

        try {
            $this->service->verifyOtp('a@b.com', '0000');
            $this->fail('Expected PasswordResetException was not thrown.');
        } catch (PasswordResetException) {
            // expected
        }

        $this->assertSame(1, PasswordResetOtp::where('email', 'a@b.com')->first()->attempts);
    }

    public function test_verify_otp_throws_after_exceeding_max_attempts(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        PasswordResetOtp::create([
            'email'      => 'a@b.com',
            'otp'        => Hash::make('1234'),
            'attempts'   => PasswordResetOtp::MAX_ATTEMPTS,
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->expectException(PasswordResetException::class);

        $this->service->verifyOtp('a@b.com', '1234');
    }

    public function test_verify_otp_throws_when_expired(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        PasswordResetOtp::create([
            'email'      => 'a@b.com',
            'otp'        => Hash::make('1234'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->expectException(PasswordResetException::class);

        $this->service->verifyOtp('a@b.com', '1234');
    }

    public function test_reset_password_updates_the_users_password(): void
    {
        $user = User::factory()->create(['email' => 'a@b.com']);
        $this->service->sendOtp('a@b.com');
        $resetToken = $this->service->verifyOtp('a@b.com', '1234');

        $this->service->resetPassword('a@b.com', $resetToken, 'NewPassword123');

        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_reset_password_revokes_existing_tokens_and_consumes_the_record(): void
    {
        $user = User::factory()->create(['email' => 'a@b.com']);
        $user->createToken('mobile-app');
        $this->service->sendOtp('a@b.com');
        $resetToken = $this->service->verifyOtp('a@b.com', '1234');

        $this->service->resetPassword('a@b.com', $resetToken, 'NewPassword123');

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseMissing('password_reset_otps', ['email' => 'a@b.com']);
    }

    public function test_reset_password_throws_for_invalid_token(): void
    {
        $user = User::factory()->create(['email' => 'a@b.com']);
        $this->service->sendOtp('a@b.com');
        $this->service->verifyOtp('a@b.com', '1234');

        $this->expectException(PasswordResetException::class);

        $this->service->resetPassword('a@b.com', 'wrong-token', 'NewPassword123');
    }

    public function test_reset_password_throws_when_no_reset_token_was_issued(): void
    {
        User::factory()->create(['email' => 'a@b.com']);
        $this->service->sendOtp('a@b.com');

        $this->expectException(PasswordResetException::class);

        $this->service->resetPassword('a@b.com', 'anything', 'NewPassword123');
    }
}

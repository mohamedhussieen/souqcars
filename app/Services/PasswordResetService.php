<?php

namespace App\Services;

use App\Exceptions\PasswordResetException;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/** Handles the forgot-password business logic: OTP issuance, verification, and password reset. */
class PasswordResetService
{
    private const OTP_TTL_MINUTES = 10;

    private const RESET_TOKEN_TTL_MINUTES = 10;

    /**
     * Generates a 4-digit OTP and emails it if the address belongs to a user.
     * Always returns the same outcome shape regardless of whether the email exists,
     * so the API response can't be used to enumerate registered users.
     */
    public function sendOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return;
        }

        PasswordResetOtp::where('email', $email)->delete();

        // Static OTP for testing; revert to random_int generation before production.
        $otp = '1234';

        PasswordResetOtp::create([
            'email'      => $email,
            'otp'        => Hash::make($otp),
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
        ]);

        Mail::to($email)->send(new PasswordResetOtpMail($otp));
    }

    /**
     * Verifies the OTP for the given email, consumes it, and issues a reset token.
     * Throws PasswordResetException with a specific reason on any failure.
     */
    public function verifyOtp(string $email, string $otp): string
    {
        $record = PasswordResetOtp::where('email', $email)->latest()->first();

        if (!$record) {
            throw PasswordResetException::invalidOtp();
        }

        if ($record->hasExceededAttempts()) {
            throw PasswordResetException::tooManyAttempts();
        }

        if ($record->isExpired()) {
            throw PasswordResetException::otpExpired();
        }

        if (!Hash::check($otp, $record->otp)) {
            $record->increment('attempts');

            throw PasswordResetException::invalidOtp();
        }

        $resetToken = Str::random(60);

        $record->update([
            'reset_token' => Hash::make($resetToken),
            'expires_at'  => now()->addMinutes(self::RESET_TOKEN_TTL_MINUTES),
        ]);

        return $resetToken;
    }

    /**
     * Verifies the reset token for the given email and, if valid, updates the password.
     * Consumes the token and revokes all existing Sanctum tokens for the user.
     */
    public function resetPassword(string $email, string $resetToken, string $password): void
    {
        $record = PasswordResetOtp::where('email', $email)->latest()->first();

        if (!$record || !$record->reset_token || $record->isExpired()) {
            throw PasswordResetException::invalidResetToken();
        }

        if (!Hash::check($resetToken, $record->reset_token)) {
            throw PasswordResetException::invalidResetToken();
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw PasswordResetException::invalidResetToken();
        }

        $user->update(['password' => Hash::make($password)]);
        $user->tokens()->delete();

        $record->delete();
    }
}

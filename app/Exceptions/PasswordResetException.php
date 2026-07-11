<?php

namespace App\Exceptions;

use Exception;

/** Thrown by PasswordResetService for any forgot-password-flow failure; carries its own message key and HTTP status. */
class PasswordResetException extends Exception
{
    public function __construct(private readonly string $translationKey, private readonly int $status = 400)
    {
        parent::__construct($translationKey);
    }

    public static function invalidOtp(): self
    {
        return new self('messages.auth.otp_invalid', 400);
    }

    public static function otpExpired(): self
    {
        return new self('messages.auth.otp_expired', 400);
    }

    public static function tooManyAttempts(): self
    {
        return new self('messages.auth.otp_too_many_attempts', 429);
    }

    public static function invalidResetToken(): self
    {
        return new self('messages.auth.reset_token_invalid', 400);
    }

    public function translationKey(): string
    {
        return $this->translationKey;
    }

    public function status(): int
    {
        return $this->status;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Stores a hashed OTP (and, once verified, a hashed reset token) for the forgot-password flow. */
class PasswordResetOtp extends Model
{
    protected $fillable = ['email', 'otp', 'reset_token', 'attempts', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'attempts'   => 'integer',
    ];

    /** Maximum allowed OTP verification attempts before the record is locked out. */
    public const MAX_ATTEMPTS = 5;

    /** Checks whether this OTP record has not yet expired. */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /** Checks whether the attempts counter has reached the lockout threshold. */
    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= self::MAX_ATTEMPTS;
    }
}

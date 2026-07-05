<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Stores a one-time password code for email verification. */
class OtpCode extends Model
{
    protected $fillable = ['email', 'code', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    /** Checks whether this OTP is still valid (not used and not expired). */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }
}

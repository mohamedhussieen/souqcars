<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Represents a single device's FCM push-notification token belonging to a user. */
class UserFcmToken extends Model
{
    protected $fillable = ['user_id', 'token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

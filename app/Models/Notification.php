<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Represents a single in-app notification delivered to a user (mirrored to FCM push on creation). */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'data',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'type'    => NotificationType::class,
            'data'    => 'array',
            'read_at' => 'datetime',
        ];
    }

    /** Returns the user this notification was sent to. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

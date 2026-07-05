<?php

namespace App\Models;

use App\Enums\ThemeMode;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/** Represents a car marketplace user with authentication, media, and role capabilities. */
class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'fcm_token',
        'notification_enabled',
        'theme',
        'policy_accepted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'is_active'            => 'boolean',
            'notification_enabled' => 'boolean',
            'theme'                => ThemeMode::class,
            'policy_accepted_at'   => 'datetime',
        ];
    }

    /** Registers the avatar media collection allowing only a single file. */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    /** Returns the user's avatar URL or null if none is set. */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }

    /** Returns whether the user has accepted the application policy. */
    public function hasAcceptedPolicy(): bool
    {
        return $this->policy_accepted_at !== null;
    }
}

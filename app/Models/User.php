<?php

namespace App\Models;

use App\Enums\ThemeMode;
use App\Enums\UserRole;
use App\Traits\HasOptimizedMedia;
use App\Traits\SendsFirebaseNotifications;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/** Represents a car marketplace user with authentication, media, and role capabilities. */
class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, InteractsWithMedia, HasOptimizedMedia, SendsFirebaseNotifications;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
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
        $this->registerOptimizedMediaCollection('avatar');
    }

    /** Registers the optimized (size-reduced) conversion for the avatar. */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerOptimizedConversion();
    }

    /** Returns the user's optimized avatar URL, falling back to the original, or null if none is set. */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar', 'optimized') ?: $this->getFirstMediaUrl('avatar') ?: null;
    }

    /** Returns whether the user has accepted the application policy. */
    public function hasAcceptedPolicy(): bool
    {
        return $this->policy_accepted_at !== null;
    }

    /** Returns all FCM push-notification tokens registered by this user's devices. */
    public function fcmTokens(): HasMany
    {
        return $this->hasMany(UserFcmToken::class);
    }

    /** Returns all cars this user has favorited. */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /** Returns all car ratings submitted by this user. */
    public function carRatings(): HasMany
    {
        return $this->hasMany(CarRating::class);
    }

    /** Returns all in-app notifications sent to this user. */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}

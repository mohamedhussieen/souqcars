<?php

namespace App\Models;

use App\Enums\SellerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** Represents the single admin-managed showroom profile (Phase 1); user_id is reserved for Phase 3 ownership. */
class Showroom extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'phone',
        'whatsapp',
        'email',
        'address_ar',
        'address_en',
        'lat',
        'lng',
        'rating',
        'is_verified',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'lat'         => 'decimal:7',
            'lng'         => 'decimal:7',
            'rating'      => 'decimal:2',
            'is_verified' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }

    /** Returns the Phase-3 owning user, if any. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Returns all cars listed under this showroom (scoped by seller_type/seller_id, not a plain foreign key). */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'seller_id')->where('seller_type', SellerType::Showroom->value);
    }

    /** Registers the single-file logo media collection. */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    /** No conversions defined for the showroom logo; original is served as-is. */
    public function registerMediaConversions(?Media $media = null): void
    {
        //
    }

    /** Returns the showroom's logo URL, or null if none is set. */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }
}

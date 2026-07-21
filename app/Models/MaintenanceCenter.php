<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** Represents a maintenance/service center that offers bookable services. */
class MaintenanceCenter extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'phone',
        'whatsapp',
        'address_ar',
        'address_en',
        'lat',
        'lng',
        'rating',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat'       => 'decimal:7',
            'lng'       => 'decimal:7',
            'rating'    => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /** Returns all services offered by this center. */
    public function services(): HasMany
    {
        return $this->hasMany(MaintenanceService::class);
    }

    /** Returns all bookings made at this center. */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Registers the single-file center_logo media collection. */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('center_logo')->singleFile();
    }

    /** No conversions defined for the center logo; original is served as-is. */
    public function registerMediaConversions(?Media $media = null): void
    {
        //
    }

    /** Returns the center's logo URL, or null if none is set. */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('center_logo') ?: null;
    }
}

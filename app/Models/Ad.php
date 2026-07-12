<?php

namespace App\Models;

use App\Enums\AdActionType;
use App\Enums\AdType;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** Represents a promotional ad/banner shown on the mobile home screen. */
class Ad extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title_ar',
        'title_en',
        'type',
        'action_type',
        'action_value',
        'starts_at',
        'ends_at',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type'        => AdType::class,
            'action_type' => AdActionType::class,
            'starts_at'   => 'date',
            'ends_at'     => 'date',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    /** Registers the single-file ad image media collection. */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ad_image')->singleFile();
    }

    /** No conversions defined for ad images; original is served as-is. */
    public function registerMediaConversions(?Media $media = null): void
    {
        //
    }

    /** Returns the ad's image URL, or null if none is set. */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('ad_image') ?: null;
    }
}

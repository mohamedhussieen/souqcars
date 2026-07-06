<?php

namespace App\Models;

use App\Traits\HasOptimizedMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** Represents a car brand (manufacturer) with associated models. */
class Brand extends Model implements HasMedia
{
    use InteractsWithMedia, HasOptimizedMedia;

    protected $fillable = ['name_ar', 'name_en'];

    /** Returns all car models belonging to this brand. */
    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }

    /** Registers the logo media collection allowing only a single file. */
    public function registerMediaCollections(): void
    {
        $this->registerOptimizedMediaCollection('logo');
    }

    /** Registers the optimized (size-reduced) conversion for the logo. */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerOptimizedConversion();
    }

    /** Returns the brand's optimized logo URL, falling back to the original, or null if none is set. */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'optimized') ?: $this->getFirstMediaUrl('logo') ?: null;
    }
}

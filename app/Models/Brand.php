<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/** Represents a car brand (manufacturer) with associated models. */
class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name_ar', 'name_en'];

    /** Returns all car models belonging to this brand. */
    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }

    /** Registers the logo media collection allowing only a single file. */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    /** Returns the brand's logo URL or null if none is set. */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }
}

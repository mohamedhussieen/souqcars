<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Represents a specific car model belonging to a brand. */
class CarModel extends Model
{
    use HasFactory;

    protected $fillable = ['brand_id', 'name_ar', 'name_en'];

    /** Returns the brand that manufactures this model. */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** Returns all cars listed under this model. */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}

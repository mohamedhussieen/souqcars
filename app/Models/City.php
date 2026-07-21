<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Represents an Egyptian city available for selection in the marketplace. */
class City extends Model
{
    use HasFactory;

    protected $fillable = ['name_ar', 'name_en'];

    /** Returns all cars listed in this city. */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}

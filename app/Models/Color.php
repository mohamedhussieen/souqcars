<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Represents a car color lookup entity (bilingual name, active flag). */
class Color extends Model
{
    use HasFactory;

    protected $fillable = ['name_ar', 'name_en', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Returns all cars painted this color. */
    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}

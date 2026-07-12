<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Represents a single user's rating/comment on a car listing. */
class CarRating extends Model
{
    protected $fillable = ['user_id', 'car_id', 'rating', 'comment'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    /** Returns the user who submitted this rating. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Returns the car being rated. */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}

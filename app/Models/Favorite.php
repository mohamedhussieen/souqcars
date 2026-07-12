<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Represents a user's bookmark of a car listing. */
class Favorite extends Model
{
    protected $fillable = ['user_id', 'car_id'];

    /** Returns the user who favorited the car. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Returns the favorited car. */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}

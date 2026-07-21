<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Represents a user's request to be notified when a matching brand/model car becomes available. */
class CarWatchRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_id',
        'car_model_id',
        'is_active',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'notified_at' => 'datetime',
        ];
    }

    /** Returns the user who created this watch request. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Returns the watched brand. */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** Returns the watched car model. */
    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }
}

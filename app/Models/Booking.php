<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Represents a user's booking of a maintenance service at a maintenance center. */
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'maintenance_center_id',
        'maintenance_service_id',
        'car_id',
        'status',
        'date',
        'time',
        'price',
        'notes',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'date'   => 'date:Y-m-d',
            'price'  => 'decimal:2',
        ];
    }

    /** Returns the user who made this booking. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Returns the maintenance center this booking is at. */
    public function maintenanceCenter(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCenter::class);
    }

    /** Returns the specific service booked. */
    public function maintenanceService(): BelongsTo
    {
        return $this->belongsTo(MaintenanceService::class);
    }

    /** Returns the car this booking relates to, if any. */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}

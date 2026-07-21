<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Represents a single bookable service offered by a maintenance center. */
class MaintenanceService extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_center_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /** Returns the maintenance center this service belongs to. */
    public function maintenanceCenter(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCenter::class);
    }

    /** Returns all bookings made for this service. */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

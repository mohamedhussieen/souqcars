<?php

namespace App\Enums;

/** Defines the lifecycle states of a maintenance booking. */
enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}

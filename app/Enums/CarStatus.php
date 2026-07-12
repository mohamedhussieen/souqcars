<?php

namespace App\Enums;

/** Defines the lifecycle states of a car listing. */
enum CarStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case NeedsInspection = 'needs_inspection';
    case Rejected = 'rejected';
    case Sold = 'sold';
}

<?php

namespace App\Enums;

/** Defines a car's body style. */
enum BodyType: string
{
    case Sedan = 'sedan';
    case Suv = 'suv';
    case Hatchback = 'hatchback';
    case Coupe = 'coupe';
    case Pickup = 'pickup';
    case Van = 'van';
    case Other = 'other';
}

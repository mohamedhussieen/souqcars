<?php

namespace App\Enums;

/** Defines the transmission type of a car. */
enum Transmission: string
{
    case Automatic = 'automatic';
    case Manual = 'manual';
}

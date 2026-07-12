<?php

namespace App\Enums;

/** Defines the placement/purpose of an ad. */
enum AdType: string
{
    case Banner = 'banner';
    case SellYourCar = 'sell_your_car';
    case Service = 'service';
}

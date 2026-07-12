<?php

namespace App\Enums;

/** Defines a car's fuel type. */
enum FuelType: string
{
    case Petrol = 'petrol';
    case Diesel = 'diesel';
    case Electric = 'electric';
    case Hybrid = 'hybrid';
}

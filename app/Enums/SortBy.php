<?php

namespace App\Enums;

/** Defines the sort orders available for car listing queries. */
enum SortBy: string
{
    case Newest = 'newest';
    case Oldest = 'oldest';
    case PriceAsc = 'price_asc';
    case PriceDesc = 'price_desc';
    case MileageAsc = 'mileage_asc';
}

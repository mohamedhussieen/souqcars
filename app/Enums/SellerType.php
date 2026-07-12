<?php

namespace App\Enums;

/** Defines who is listing a car: the platform itself, an individual user, or a showroom. */
enum SellerType: string
{
    case Admin = 'admin';
    case Individual = 'individual';
    case Showroom = 'showroom';
}

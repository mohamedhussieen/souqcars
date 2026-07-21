<?php

namespace App\Enums;

/** Defines every kind of notification the platform can send to a user. */
enum NotificationType: string
{
    case CarMatch = 'car_match';
    case ShowroomReply = 'showroom_reply';
    case BookingConfirmed = 'booking_confirmed';
    case BookingCancelled = 'booking_cancelled';
    case PriceDrop = 'price_drop';
    case ListingApproved = 'listing_approved';
    case ListingRejected = 'listing_rejected';
    case CarAvailable = 'car_available';
}

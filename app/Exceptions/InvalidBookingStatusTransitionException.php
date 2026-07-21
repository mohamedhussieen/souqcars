<?php

namespace App\Exceptions;

use Exception;

/** Thrown by BookingService::updateStatus() when the requested status transition is not allowed. */
class InvalidBookingStatusTransitionException extends Exception
{
    public function __construct(private readonly string $translationKey = 'messages.bookings.invalid_transition', private readonly int $status = 422)
    {
        parent::__construct($translationKey);
    }

    public function translationKey(): string
    {
        return $this->translationKey;
    }

    public function status(): int
    {
        return $this->status;
    }
}

<?php

namespace App\Exceptions;

use Exception;

/** Thrown by BookingService::create() when the user already has a pending/confirmed booking at the same center on the same date. */
class BookingConflictException extends Exception
{
    public function __construct(private readonly string $translationKey = 'messages.bookings.conflict', private readonly int $status = 422)
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

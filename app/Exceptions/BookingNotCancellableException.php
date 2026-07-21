<?php

namespace App\Exceptions;

use Exception;

/** Thrown by BookingService::cancel() when the booking is not in a pending/confirmed state. */
class BookingNotCancellableException extends Exception
{
    public function __construct(private readonly string $translationKey = 'messages.bookings.not_cancellable', private readonly int $status = 422)
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

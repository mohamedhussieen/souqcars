<?php

namespace App\Exceptions;

use Exception;

/** Thrown when deleting a record is blocked because dependent records still reference it (e.g. a brand with cars, a center with bookings). */
class HasDependentRecordsException extends Exception
{
    public function __construct(private readonly string $translationKey, private readonly int $status = 422)
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

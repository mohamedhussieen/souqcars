<?php

namespace App\Exceptions;

use Exception;

/** Thrown when a user attempts to access or modify a record (booking, notification, watch request) they don't own. */
class NotOwnerException extends Exception
{
    public function __construct(private readonly string $translationKey, private readonly int $status = 403)
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

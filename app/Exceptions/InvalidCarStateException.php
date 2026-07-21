<?php

namespace App\Exceptions;

use Exception;

/** Thrown when an action requires the car to be in a specific status it currently isn't in. */
class InvalidCarStateException extends Exception
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

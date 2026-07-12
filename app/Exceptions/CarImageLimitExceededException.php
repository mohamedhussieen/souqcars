<?php

namespace App\Exceptions;

use Exception;

/** Thrown by CarService::uploadImages() when a car would exceed the 10-image gallery limit. */
class CarImageLimitExceededException extends Exception
{
    public function __construct(private readonly string $translationKey = 'messages.cars.image_limit_exceeded', private readonly int $status = 422)
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

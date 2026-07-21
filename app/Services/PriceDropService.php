<?php

namespace App\Services;

use App\Enums\CarStatus;
use App\Enums\NotificationType;
use App\Models\Car;
use App\Models\Favorite;

/** Notifies users who favorited a car when its price drops. */
class PriceDropService
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    /**
     * Sends a price_drop notification to every user who favorited this car, if the price actually
     * decreased and the car is still active. Intended to be called only from SendPriceDropJob.
     */
    public function notifyIfDropped(Car $car, float $oldPrice): void
    {
        if ($car->status !== CarStatus::Active || (float) $car->price >= $oldPrice) {
            return;
        }

        $users = Favorite::query()
            ->where('car_id', $car->id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        foreach ($users as $user) {
            $this->notificationService->send($user, NotificationType::PriceDrop, [
                'car_id' => $car->id,
            ]);
        }
    }
}

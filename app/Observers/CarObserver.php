<?php

namespace App\Observers;

use App\Enums\CarStatus;
use App\Jobs\SendPriceDropJob;
use App\Jobs\SendWatchNotificationsJob;
use App\Models\Car;

/** Dispatches watch-request and price-drop notification jobs in reaction to car lifecycle changes. */
class CarObserver
{
    /** Notifies matching watch requests when a new car is created directly as active. */
    public function created(Car $car): void
    {
        if ($car->status === CarStatus::Active) {
            SendWatchNotificationsJob::dispatch($car);
        }
    }

    /** Dispatches price-drop notifications on a price decrease, and watch notifications when the car just became active. */
    public function updated(Car $car): void
    {
        if ($car->status === CarStatus::Active && $car->wasChanged('price')) {
            $oldPrice = (float) $car->getOriginal('price');

            if ((float) $car->price < $oldPrice) {
                SendPriceDropJob::dispatch($car, $oldPrice);
            }
        }

        if ($car->wasChanged('status') && $car->status === CarStatus::Active) {
            SendWatchNotificationsJob::dispatch($car);
        }
    }
}

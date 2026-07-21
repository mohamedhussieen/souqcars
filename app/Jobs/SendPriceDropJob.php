<?php

namespace App\Jobs;

use App\Models\Car;
use App\Services\PriceDropService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

/** Notifies users who favorited a car when its price drops. */
class SendPriceDropJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Car $car, public readonly float $oldPrice)
    {
        $this->onQueue('notifications');
    }

    /** Returns the middleware this job should pass through, preventing overlapping runs for the same car. */
    public function middleware(): array
    {
        return [new WithoutOverlapping("price-drop-car-{$this->car->id}")];
    }

    /** Delegates to PriceDropService to notify users who favorited this car. */
    public function handle(PriceDropService $priceDropService): void
    {
        $priceDropService->notifyIfDropped($this->car, $this->oldPrice);
    }
}

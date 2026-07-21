<?php

namespace App\Jobs;

use App\Models\Car;
use App\Services\WatchRequestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

/** Notifies every matching watch request when a car becomes active. */
class SendWatchNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Car $car)
    {
        $this->onQueue('notifications');
    }

    /** Returns the middleware this job should pass through, preventing overlapping runs for the same car. */
    public function middleware(): array
    {
        return [new WithoutOverlapping("watch-notifications-car-{$this->car->id}")];
    }

    /** Delegates to WatchRequestService to find and notify matching watch requests. */
    public function handle(WatchRequestService $watchRequestService): void
    {
        $watchRequestService->notifyMatches($this->car);
    }
}

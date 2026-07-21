<?php

namespace App\Http\Controllers\Api\Mobile\WatchRequests;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Car;
use App\Services\WatchRequestService;
use Illuminate\Http\Request;

/** Deactivates the authenticated user's watch request for a car's brand/model. */
class UnwatchCarController extends BaseApiController
{
    public function __construct(private readonly WatchRequestService $service)
    {
    }

    /** Removes the watch request. */
    public function __invoke(Request $request, Car $car)
    {
        $this->service->unwatch($request->user(), $car);

        return $this->success(null, __('messages.watch_requests.unwatched'));
    }
}

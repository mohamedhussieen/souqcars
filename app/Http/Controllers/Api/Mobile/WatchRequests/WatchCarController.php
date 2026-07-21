<?php

namespace App\Http\Controllers\Api\Mobile\WatchRequests;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\WatchRequestResource;
use App\Models\Car;
use App\Services\WatchRequestService;
use Illuminate\Http\Request;

/** Creates a "notify me" watch request for a sold car's brand/model. */
class WatchCarController extends BaseApiController
{
    public function __construct(private readonly WatchRequestService $service)
    {
    }

    /** Registers the watch request for the authenticated user. */
    public function __invoke(Request $request, Car $car)
    {
        $watchRequest = $this->service->watch($request->user(), $car);
        $watchRequest->load(['brand', 'carModel']);

        return $this->success(new WatchRequestResource($watchRequest), __('messages.watch_requests.watched'));
    }
}

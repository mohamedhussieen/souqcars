<?php

namespace App\Http\Controllers\Api\Mobile\WatchRequests;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\WatchRequestResource;
use App\Services\WatchRequestService;
use Illuminate\Http\Request;

/** Returns all of the authenticated user's watch requests. */
class ListWatchRequestsController extends BaseApiController
{
    public function __construct(private readonly WatchRequestService $service)
    {
    }

    /** Fetches the user's watch requests. */
    public function __invoke(Request $request)
    {
        $watchRequests = $this->service->list($request->user());

        return $this->success(WatchRequestResource::collection($watchRequests), __('messages.watch_requests.fetched'));
    }
}

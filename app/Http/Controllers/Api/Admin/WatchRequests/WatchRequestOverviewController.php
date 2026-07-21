<?php

namespace App\Http\Controllers\Api\Admin\WatchRequests;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\Admin\WatchRequestOverviewResource;
use App\Services\WatchRequestService;

/** Returns a paginated overview of watch-request demand grouped by brand/model. */
class WatchRequestOverviewController extends BaseApiController
{
    public function __construct(private readonly WatchRequestService $service)
    {
    }

    /** Fetches the grouped demand overview, most-watched first. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->service->adminOverview($request->perPage());

        $paginator->setCollection(
            collect(WatchRequestOverviewResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.watch_requests_fetched'));
    }
}

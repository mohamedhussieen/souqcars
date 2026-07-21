<?php

namespace App\Http\Controllers\Api\Mobile\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\MaintenanceCenterResource;
use App\Services\MaintenanceCenterService;

/** Returns a paginated list of active maintenance centers. */
class ListMaintenanceCentersController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Fetches active maintenance centers, newest first. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->service->list($request->perPage());

        $paginator->setCollection(
            collect(MaintenanceCenterResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.maintenance.centers_fetched'));
    }
}

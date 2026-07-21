<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\Admin\MaintenanceCenterAdminResource;
use App\Services\MaintenanceCenterService;

/** Returns a paginated, filterable admin list of all maintenance centers. */
class ListMaintenanceCentersController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Fetches maintenance centers, optionally filtered by search/active state. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->service->adminList(
            $request->perPage(),
            $request->string('search')->toString() ?: null,
            $request->has('is_active') ? $request->boolean('is_active') : null,
        );

        $paginator->setCollection(
            collect(MaintenanceCenterAdminResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.maintenance_centers_fetched'));
    }
}

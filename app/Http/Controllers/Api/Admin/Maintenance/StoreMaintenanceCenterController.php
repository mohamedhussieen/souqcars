<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\CreateMaintenanceCenterRequest;
use App\Http\Resources\Admin\MaintenanceCenterAdminResource;
use App\Services\MaintenanceCenterService;

/** Creates a new maintenance center with an optional logo upload. */
class StoreMaintenanceCenterController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Persists the new maintenance center and returns it. */
    public function __invoke(CreateMaintenanceCenterRequest $request)
    {
        $center = $this->service->create($request->safe()->except('logo'), $request->file('logo'));

        return $this->success(new MaintenanceCenterAdminResource($center), __('messages.admin.maintenance_center_created'), 201);
    }
}

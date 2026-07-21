<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\CreateMaintenanceServiceRequest;
use App\Http\Resources\Admin\MaintenanceServiceAdminResource;
use App\Models\MaintenanceCenter;
use App\Services\MaintenanceCenterService;

/** Creates a new service under a maintenance center. */
class StoreMaintenanceServiceController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Persists the new service and returns it. */
    public function __invoke(CreateMaintenanceServiceRequest $request, MaintenanceCenter $center)
    {
        $created = $this->service->createService($center, $request->validated());

        return $this->success(new MaintenanceServiceAdminResource($created), __('messages.admin.maintenance_service_created'), 201);
    }
}

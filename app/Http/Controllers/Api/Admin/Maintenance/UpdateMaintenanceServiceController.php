<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateMaintenanceServiceRequest;
use App\Http\Resources\Admin\MaintenanceServiceAdminResource;
use App\Models\MaintenanceService;
use App\Services\MaintenanceCenterService;

/** Updates an existing maintenance service's fields. */
class UpdateMaintenanceServiceController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Persists the changes and returns the updated service. */
    public function __invoke(UpdateMaintenanceServiceRequest $request, MaintenanceService $service)
    {
        $updated = $this->service->updateService($service, $request->validated());

        return $this->success(new MaintenanceServiceAdminResource($updated), __('messages.admin.maintenance_service_updated'));
    }
}

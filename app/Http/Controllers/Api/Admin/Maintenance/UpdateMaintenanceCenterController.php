<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateMaintenanceCenterRequest;
use App\Http\Resources\Admin\MaintenanceCenterAdminResource;
use App\Models\MaintenanceCenter;
use App\Services\MaintenanceCenterService;

/** Updates an existing maintenance center's fields. */
class UpdateMaintenanceCenterController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Persists the changes and returns the updated center. */
    public function __invoke(UpdateMaintenanceCenterRequest $request, MaintenanceCenter $center)
    {
        $updated = $this->service->update($center, $request->validated());

        return $this->success(new MaintenanceCenterAdminResource($updated), __('messages.admin.maintenance_center_updated'));
    }
}

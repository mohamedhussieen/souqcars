<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\MaintenanceService;
use App\Services\MaintenanceCenterService;

/** Deletes a maintenance service, blocked if it has pending/confirmed bookings. */
class DeleteMaintenanceServiceController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Deletes the service. */
    public function __invoke(MaintenanceService $service)
    {
        $this->service->deleteService($service);

        return $this->success(null, __('messages.admin.maintenance_service_deleted'));
    }
}

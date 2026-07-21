<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\MaintenanceCenter;
use App\Services\MaintenanceCenterService;

/** Deletes a maintenance center, blocked if it has pending/confirmed bookings. */
class DeleteMaintenanceCenterController extends BaseApiController
{
    public function __construct(private readonly MaintenanceCenterService $service)
    {
    }

    /** Soft-deletes the center. */
    public function __invoke(MaintenanceCenter $center)
    {
        $this->service->delete($center);

        return $this->success(null, __('messages.admin.maintenance_center_deleted'));
    }
}

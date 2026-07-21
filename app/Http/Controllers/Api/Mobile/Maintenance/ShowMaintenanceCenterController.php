<?php

namespace App\Http\Controllers\Api\Mobile\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\MaintenanceCenterDetailResource;
use App\Models\MaintenanceCenter;

/** Returns a single maintenance center's public profile with its active services. */
class ShowMaintenanceCenterController extends BaseApiController
{
    /** Shows the center with its active services eager-loaded. */
    public function __invoke(MaintenanceCenter $center)
    {
        $center->load(['services' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')]);

        return $this->success(new MaintenanceCenterDetailResource($center), __('messages.maintenance.center_fetched'));
    }
}

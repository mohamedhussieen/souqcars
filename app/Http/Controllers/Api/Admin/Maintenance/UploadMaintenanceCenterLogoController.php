<?php

namespace App\Http\Controllers\Api\Admin\Maintenance;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UploadMaintenanceCenterLogoRequest;
use App\Models\MaintenanceCenter;

/** Uploads or replaces a maintenance center's logo. */
class UploadMaintenanceCenterLogoController extends BaseApiController
{
    /** Attaches the logo via Media Library and returns its URL. */
    public function __invoke(UploadMaintenanceCenterLogoRequest $request, MaintenanceCenter $center)
    {
        $center->addMedia($request->file('logo'))->toMediaCollection('center_logo');

        return $this->success(['logo_url' => $center->fresh()->logo_url], __('messages.admin.maintenance_center_logo_uploaded'));
    }
}

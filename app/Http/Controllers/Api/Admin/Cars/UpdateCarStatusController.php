<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Enums\CarStatus;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateCarStatusRequest;
use App\Http\Resources\Admin\CarAdminResource;
use App\Models\Car;

/** Transitions a car's status (approve, reject with reason, etc.). */
class UpdateCarStatusController extends BaseApiController
{
    /** Applies the status change, storing the rejection reason only when rejecting. */
    public function __invoke(UpdateCarStatusRequest $request, Car $car)
    {
        $status = $request->input('status');

        $car->update([
            'status'           => $status,
            'rejection_reason' => $status === CarStatus::Rejected->value ? $request->input('rejection_reason') : null,
        ]);

        return $this->success(new CarAdminResource($car->fresh()), __('messages.admin.car_status_updated'));
    }
}

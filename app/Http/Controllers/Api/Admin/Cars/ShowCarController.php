<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Admin\CarAdminResource;
use App\Models\Car;

/** Returns a single car with all raw bilingual fields for the admin dashboard. */
class ShowCarController extends BaseApiController
{
    /** Shows the car regardless of status. */
    public function __invoke(Car $car)
    {
        return $this->success(new CarAdminResource($car), __('messages.admin.car_fetched'));
    }
}

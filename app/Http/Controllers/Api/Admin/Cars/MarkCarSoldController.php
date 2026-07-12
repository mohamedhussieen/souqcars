<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Admin\CarAdminResource;
use App\Models\Car;
use App\Services\CarService;

/** Marks a car listing as sold. */
class MarkCarSoldController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Transitions the car to sold and returns it. */
    public function __invoke(Car $car)
    {
        $this->carService->markAsSold($car);

        return $this->success(new CarAdminResource($car->fresh()), __('messages.admin.car_marked_sold'));
    }
}

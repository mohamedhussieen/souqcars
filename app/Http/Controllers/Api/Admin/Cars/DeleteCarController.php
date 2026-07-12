<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Car;
use App\Services\CarService;

/** Soft-deletes a car listing. */
class DeleteCarController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Soft-deletes the car and returns a confirmation. */
    public function __invoke(Car $car)
    {
        $this->carService->delete($car);

        return $this->success(null, __('messages.admin.car_deleted'));
    }
}

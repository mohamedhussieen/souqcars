<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateCarRequest;
use App\Http\Resources\Admin\CarAdminResource;
use App\Models\Car;
use App\Services\CarService;

/** Updates an existing car's attributes. */
class UpdateCarController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Applies the validated changes and returns the updated car. */
    public function __invoke(UpdateCarRequest $request, Car $car)
    {
        $car = $this->carService->update($car, $request->validated());

        return $this->success(new CarAdminResource($car), __('messages.admin.car_updated'));
    }
}

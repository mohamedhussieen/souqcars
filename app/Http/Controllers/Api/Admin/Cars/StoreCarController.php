<?php

namespace App\Http\Controllers\Api\Admin\Cars;

use App\Enums\SellerType;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\CreateCarRequest;
use App\Http\Resources\Admin\CarAdminResource;
use App\Services\CarService;

/** Creates a new admin-listed car with optional gallery images and inspection report. */
class StoreCarController extends BaseApiController
{
    public function __construct(private readonly CarService $carService) {}

    /** Persists the car (seller forced to admin/null in Phase 1) and returns it. */
    public function __invoke(CreateCarRequest $request)
    {
        $data = $request->safe()->except(['images', 'inspection_file']);
        $data['seller_type'] = SellerType::Admin->value;
        $data['seller_id']   = null;

        $car = $this->carService->create(
            $data,
            $request->file('images', []),
            $request->file('inspection_file')
        );

        return $this->success(new CarAdminResource($car), __('messages.admin.car_created'), 201);
    }
}

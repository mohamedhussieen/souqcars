<?php

namespace App\Http\Controllers\Api\Admin\CarModels;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\StoreCarModelRequest;
use App\Http\Resources\Admin\AdminCarModelResource;
use App\Services\AdminCarModelService;

/** Creates a new car model under a brand. */
class StoreCarModelController extends BaseApiController
{
    public function __construct(private readonly AdminCarModelService $adminCarModelService) {}

    /** Persists the new car model and returns it. */
    public function __invoke(StoreCarModelRequest $request)
    {
        $carModel = $this->adminCarModelService->create($request->validated());

        return $this->success(
            new AdminCarModelResource($carModel),
            __('messages.admin.car_model_created'),
            201
        );
    }
}

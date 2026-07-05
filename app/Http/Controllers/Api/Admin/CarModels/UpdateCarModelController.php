<?php

namespace App\Http\Controllers\Api\Admin\CarModels;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateCarModelRequest;
use App\Http\Resources\Admin\AdminCarModelResource;
use App\Models\CarModel;
use App\Services\AdminCarModelService;

/** Updates an existing car model's brand and bilingual names. */
class UpdateCarModelController extends BaseApiController
{
    public function __construct(private readonly AdminCarModelService $adminCarModelService) {}

    /** Persists changes to the given car model (route-model bound) and returns it. */
    public function __invoke(UpdateCarModelRequest $request, CarModel $carModel)
    {
        $updated = $this->adminCarModelService->update($carModel, $request->validated());

        return $this->success(
            new AdminCarModelResource($updated),
            __('messages.admin.car_model_updated')
        );
    }
}

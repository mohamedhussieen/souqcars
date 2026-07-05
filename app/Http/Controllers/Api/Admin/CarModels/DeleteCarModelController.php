<?php

namespace App\Http\Controllers\Api\Admin\CarModels;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\CarModel;
use App\Services\AdminCarModelService;

/** Deletes a car model. */
class DeleteCarModelController extends BaseApiController
{
    public function __construct(private readonly AdminCarModelService $adminCarModelService) {}

    /** Removes the given car model (route-model bound). */
    public function __invoke(CarModel $carModel)
    {
        $this->adminCarModelService->delete($carModel);

        return $this->success(null, __('messages.admin.car_model_deleted'));
    }
}

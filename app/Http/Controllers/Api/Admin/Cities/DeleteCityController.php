<?php

namespace App\Http\Controllers\Api\Admin\Cities;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\City;
use App\Services\AdminCityService;

/** Deletes a city. */
class DeleteCityController extends BaseApiController
{
    public function __construct(private readonly AdminCityService $adminCityService) {}

    /** Removes the given city (route-model bound). */
    public function __invoke(City $city)
    {
        $this->adminCityService->delete($city);

        return $this->success(null, __('messages.admin.city_deleted'));
    }
}

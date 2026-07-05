<?php

namespace App\Http\Controllers\Api\Admin\Cities;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Http\Resources\Admin\AdminCityResource;
use App\Models\City;
use App\Services\AdminCityService;

/** Updates an existing city's bilingual names. */
class UpdateCityController extends BaseApiController
{
    public function __construct(private readonly AdminCityService $adminCityService) {}

    /** Persists changes to the given city (route-model bound) and returns it. */
    public function __invoke(UpdateCityRequest $request, City $city)
    {
        $updated = $this->adminCityService->update($city, $request->validated());

        return $this->success(
            new AdminCityResource($updated),
            __('messages.admin.city_updated')
        );
    }
}

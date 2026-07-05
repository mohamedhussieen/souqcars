<?php

namespace App\Http\Controllers\Api\Admin\Cities;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Resources\Admin\AdminCityResource;
use App\Services\AdminCityService;

/** Creates a new city. */
class StoreCityController extends BaseApiController
{
    public function __construct(private readonly AdminCityService $adminCityService) {}

    /** Persists the new city and returns it. */
    public function __invoke(StoreCityRequest $request)
    {
        $city = $this->adminCityService->create($request->validated());

        return $this->success(
            new AdminCityResource($city),
            __('messages.admin.city_created'),
            201
        );
    }
}

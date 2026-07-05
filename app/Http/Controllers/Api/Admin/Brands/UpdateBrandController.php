<?php

namespace App\Http\Controllers\Api\Admin\Brands;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Http\Resources\Admin\AdminBrandResource;
use App\Models\Brand;
use App\Services\AdminBrandService;

/** Updates an existing brand's names and optionally replaces its logo. */
class UpdateBrandController extends BaseApiController
{
    public function __construct(private readonly AdminBrandService $adminBrandService) {}

    /** Persists changes to the given brand (route-model bound) and returns it. */
    public function __invoke(UpdateBrandRequest $request, Brand $brand)
    {
        $updated = $this->adminBrandService->update(
            $brand,
            $request->safe()->except('logo'),
            $request->file('logo')
        );

        return $this->success(
            new AdminBrandResource($updated),
            __('messages.admin.brand_updated')
        );
    }
}

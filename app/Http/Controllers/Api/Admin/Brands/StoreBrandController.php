<?php

namespace App\Http\Controllers\Api\Admin\Brands;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Resources\Admin\AdminBrandResource;
use App\Services\AdminBrandService;

/** Creates a new brand with an optional logo upload. */
class StoreBrandController extends BaseApiController
{
    public function __construct(private readonly AdminBrandService $adminBrandService) {}

    /** Persists the new brand and returns it. */
    public function __invoke(StoreBrandRequest $request)
    {
        $brand = $this->adminBrandService->create(
            $request->safe()->except('logo'),
            $request->file('logo')
        );

        return $this->success(
            new AdminBrandResource($brand),
            __('messages.admin.brand_created'),
            201
        );
    }
}

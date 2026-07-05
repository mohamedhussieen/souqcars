<?php

namespace App\Http\Controllers\Api\Admin\Brands;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Brand;
use App\Services\AdminBrandService;

/** Deletes a brand along with its car models. */
class DeleteBrandController extends BaseApiController
{
    public function __construct(private readonly AdminBrandService $adminBrandService) {}

    /** Removes the given brand (route-model bound). */
    public function __invoke(Brand $brand)
    {
        $this->adminBrandService->delete($brand);

        return $this->success(null, __('messages.admin.brand_deleted'));
    }
}

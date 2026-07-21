<?php

namespace App\Http\Controllers\Api\Admin\Brands;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UploadLogoRequest;
use App\Models\Brand;
use App\Services\AdminBrandService;

/** Uploads or replaces a brand's logo. */
class UploadBrandLogoController extends BaseApiController
{
    public function __construct(private readonly AdminBrandService $adminBrandService)
    {
    }

    /** Stores the logo and returns its URL. */
    public function __invoke(UploadLogoRequest $request, Brand $brand)
    {
        $updated = $this->adminBrandService->update($brand, [], $request->file('logo'));

        return $this->success(['logo_url' => $updated->logo_url], __('messages.admin.brand_logo_uploaded'));
    }
}

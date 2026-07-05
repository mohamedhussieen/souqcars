<?php

namespace App\Http\Controllers\Api\Admin\Brands;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\Admin\AdminBrandResource;
use App\Services\AdminBrandService;

/** Returns a paginated list of brands for the admin dashboard. */
class ListBrandsController extends BaseApiController
{
    public function __construct(private readonly AdminBrandService $adminBrandService) {}

    /** Fetches brands paginated via AdminBrandService. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->adminBrandService->list($request->perPage());

        $paginator->setCollection(
            collect(AdminBrandResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.brands_fetched'));
    }
}

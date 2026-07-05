<?php

namespace App\Http\Controllers\Api\Mobile\Lookup;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Models\Brand;
use App\Services\LookupService;

/** Returns a paginated list of car models for a specific brand. */
class BrandModelsController extends BaseApiController
{
    public function __construct(private readonly LookupService $lookupService) {}

    /** Fetches models for the given brand (route-model bound) paginated and locale-aware. */
    public function __invoke(PaginationRequest $request, Brand $brand)
    {
        $paginator = $this->lookupService->getModelsByBrand($brand, $request->perPage());

        return $this->successPaginated($paginator, __('messages.lookup.models_fetched'));
    }
}

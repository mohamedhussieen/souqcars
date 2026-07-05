<?php

namespace App\Http\Controllers\Api\Mobile\Lookup;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Services\LookupService;

/** Returns a paginated list of car brands. */
class BrandsController extends BaseApiController
{
    public function __construct(private readonly LookupService $lookupService) {}

    /** Fetches brands paginated and locale-aware via LookupService. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->lookupService->getBrands($request->perPage());

        return $this->successPaginated($paginator, __('messages.lookup.brands_fetched'));
    }
}

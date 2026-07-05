<?php

namespace App\Http\Controllers\Api\Mobile\Lookup;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Services\LookupService;

/** Returns a paginated list of Egyptian cities. */
class CitiesController extends BaseApiController
{
    public function __construct(private readonly LookupService $lookupService) {}

    /** Fetches cities paginated and locale-aware via LookupService. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->lookupService->getCities($request->perPage());

        return $this->successPaginated($paginator, __('messages.lookup.cities_fetched'));
    }
}

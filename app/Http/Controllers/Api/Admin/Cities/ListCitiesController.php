<?php

namespace App\Http\Controllers\Api\Admin\Cities;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\Admin\AdminCityResource;
use App\Services\AdminCityService;

/** Returns a paginated list of cities for the admin dashboard. */
class ListCitiesController extends BaseApiController
{
    public function __construct(private readonly AdminCityService $adminCityService) {}

    /** Fetches cities paginated via AdminCityService. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->adminCityService->list($request->perPage());

        $paginator->setCollection(
            collect(AdminCityResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.cities_fetched'));
    }
}

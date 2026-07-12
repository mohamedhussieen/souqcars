<?php

namespace App\Http\Controllers\Api\Admin\Ads;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\AdResource;
use App\Services\AdService;

/** Returns all ads paginated, ordered by sort_order, for the admin dashboard. */
class ListAdsController extends BaseApiController
{
    public function __construct(private readonly AdService $adService) {}

    /** Fetches ads paginated via AdService. */
    public function __invoke(PaginationRequest $request)
    {
        $paginator = $this->adService->list($request->perPage());

        $paginator->setCollection(
            collect(AdResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.admin.ads_fetched'));
    }
}

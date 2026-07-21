<?php

namespace App\Http\Controllers\Api\Admin\Ads;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\ReorderAdsRequest;
use App\Services\AdService;

/** Bulk-updates the sort_order of multiple ads. */
class ReorderAdsController extends BaseApiController
{
    public function __construct(private readonly AdService $adService)
    {
    }

    /** Applies the new sort order for each ad in the request. */
    public function __invoke(ReorderAdsRequest $request)
    {
        $this->adService->reorder($request->validated('items'));

        return $this->success(null, __('messages.admin.ads_reordered'));
    }
}

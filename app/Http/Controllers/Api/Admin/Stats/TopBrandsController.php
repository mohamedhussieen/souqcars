<?php

namespace App\Http\Controllers\Api\Admin\Stats;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\DashboardStatsService;

/** Returns the top 5 brands by car count. */
class TopBrandsController extends BaseApiController
{
    public function __construct(private readonly DashboardStatsService $service)
    {
    }

    /** Fetches the top-brands breakdown. */
    public function __invoke()
    {
        return $this->success($this->service->topBrands(), __('messages.admin.analytics_fetched'));
    }
}

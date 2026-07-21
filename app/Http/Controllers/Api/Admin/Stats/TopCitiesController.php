<?php

namespace App\Http\Controllers\Api\Admin\Stats;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\DashboardStatsService;

/** Returns the top 5 cities by car count. */
class TopCitiesController extends BaseApiController
{
    public function __construct(private readonly DashboardStatsService $service)
    {
    }

    /** Fetches the top-cities breakdown. */
    public function __invoke()
    {
        return $this->success($this->service->topCities(), __('messages.admin.analytics_fetched'));
    }
}

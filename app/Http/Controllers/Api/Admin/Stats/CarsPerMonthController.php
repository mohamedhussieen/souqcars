<?php

namespace App\Http\Controllers\Api\Admin\Stats;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\DashboardStatsService;

/** Returns car listing counts for the last 12 months. */
class CarsPerMonthController extends BaseApiController
{
    public function __construct(private readonly DashboardStatsService $service)
    {
    }

    /** Fetches the last-12-months car creation breakdown. */
    public function __invoke()
    {
        return $this->success($this->service->carsPerMonth(), __('messages.admin.analytics_fetched'));
    }
}

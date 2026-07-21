<?php

namespace App\Http\Controllers\Api\Admin\Stats;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\DashboardStatsService;

/** Returns dashboard overview stats. */
class DashboardStatsController extends BaseApiController
{
    public function __construct(private readonly DashboardStatsService $service)
    {
    }

    /** Fetches headline counts for the dashboard. */
    public function __invoke()
    {
        return $this->success($this->service->stats(), __('messages.admin.stats_fetched'));
    }
}

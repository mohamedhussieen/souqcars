<?php

namespace App\Http\Controllers\Api\Admin\Stats;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\DashboardStatsService;

/** Returns booking counts grouped by status. */
class BookingsPerStatusController extends BaseApiController
{
    public function __construct(private readonly DashboardStatsService $service)
    {
    }

    /** Fetches the booking-status breakdown. */
    public function __invoke()
    {
        return $this->success($this->service->bookingsPerStatus(), __('messages.admin.analytics_fetched'));
    }
}

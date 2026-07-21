<?php

namespace App\Http\Controllers\Api\Mobile\Profile;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\ProfileStatsService;
use Illuminate\Http\Request;

/** Returns summary stats for the authenticated user's profile. */
class ProfileStatsController extends BaseApiController
{
    public function __construct(private readonly ProfileStatsService $service)
    {
    }

    /** Fetches favorites/views/listings counts. */
    public function __invoke(Request $request)
    {
        return $this->success($this->service->stats($request->user()), __('messages.profile_stats.fetched'));
    }
}

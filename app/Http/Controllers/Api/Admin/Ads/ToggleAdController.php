<?php

namespace App\Http\Controllers\Api\Admin\Ads;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Services\AdService;

/** Toggles an ad's active state. */
class ToggleAdController extends BaseApiController
{
    public function __construct(private readonly AdService $adService)
    {
    }

    /** Flips is_active for the given ad. */
    public function __invoke(Ad $ad)
    {
        $toggled = $this->adService->toggle($ad);

        return $this->success(new AdResource($toggled), __('messages.admin.ad_toggled'));
    }
}

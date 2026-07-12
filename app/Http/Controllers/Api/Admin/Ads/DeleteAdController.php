<?php

namespace App\Http\Controllers\Api\Admin\Ads;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Ad;
use App\Services\AdService;

/** Deletes an ad along with its media. */
class DeleteAdController extends BaseApiController
{
    public function __construct(private readonly AdService $adService) {}

    /** Removes the ad and returns a confirmation. */
    public function __invoke(Ad $ad)
    {
        $this->adService->delete($ad);

        return $this->success(null, __('messages.admin.ad_deleted'));
    }
}

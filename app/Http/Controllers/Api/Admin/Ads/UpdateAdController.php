<?php

namespace App\Http\Controllers\Api\Admin\Ads;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\UpdateAdRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Services\AdService;

/** Updates an existing ad, optionally replacing its image. */
class UpdateAdController extends BaseApiController
{
    public function __construct(private readonly AdService $adService) {}

    /** Applies the validated changes and returns the updated ad. */
    public function __invoke(UpdateAdRequest $request, Ad $ad)
    {
        $ad = $this->adService->update(
            $ad,
            $request->safe()->except('image'),
            $request->file('image')
        );

        return $this->success(new AdResource($ad), __('messages.admin.ad_updated'));
    }
}

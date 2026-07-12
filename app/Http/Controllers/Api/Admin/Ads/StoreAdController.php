<?php

namespace App\Http\Controllers\Api\Admin\Ads;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\CreateAdRequest;
use App\Http\Resources\AdResource;
use App\Services\AdService;

/** Creates a new promotional ad with an optional image. */
class StoreAdController extends BaseApiController
{
    public function __construct(private readonly AdService $adService) {}

    /** Persists the ad and returns it. */
    public function __invoke(CreateAdRequest $request)
    {
        $ad = $this->adService->create(
            $request->safe()->except('image'),
            $request->file('image')
        );

        return $this->success(new AdResource($ad), __('messages.admin.ad_created'), 201);
    }
}

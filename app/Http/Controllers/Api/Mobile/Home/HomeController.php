<?php

namespace App\Http\Controllers\Api\Mobile\Home;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\AdResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CarListResource;
use App\Http\Resources\ShowroomResource;
use App\Services\HomeService;
use Illuminate\Http\Request;

/** Returns the aggregated home-screen payload (ads, brands, car sections, showrooms). */
class HomeController extends BaseApiController
{
    public function __construct(private readonly HomeService $homeService) {}

    /** Builds the home payload for the current (possibly guest) user and wraps each section in its resource. */
    public function __invoke(Request $request)
    {
        $home = $this->homeService->build($request->user());

        return $this->success([
            'ads'           => AdResource::collection($home['ads']),
            'brands'        => BrandResource::collection($home['brands']),
            'customer_cars' => CarListResource::collection($home['customer_cars']),
            'latest_cars'   => CarListResource::collection($home['latest_cars']),
            'showrooms'     => ShowroomResource::collection($home['showrooms']),
            'featured_cars' => CarListResource::collection($home['featured_cars']),
        ], __('messages.home.fetched'));
    }
}

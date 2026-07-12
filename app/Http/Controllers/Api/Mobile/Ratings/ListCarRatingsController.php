<?php

namespace App\Http\Controllers\Api\Mobile\Ratings;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Lookup\PaginationRequest;
use App\Http\Resources\RatingResource;
use App\Models\Car;
use App\Services\RatingService;

/** Returns a paginated list of ratings for a car, most recent first. */
class ListCarRatingsController extends BaseApiController
{
    public function __construct(private readonly RatingService $ratingService) {}

    /** Fetches the car's ratings paginated via RatingService. */
    public function __invoke(PaginationRequest $request, Car $car)
    {
        $paginator = $this->ratingService->list($car, $request->perPage());

        $paginator->setCollection(
            collect(RatingResource::collection($paginator->getCollection())->toArray($request))
        );

        return $this->successPaginated($paginator, __('messages.ratings.fetched'));
    }
}

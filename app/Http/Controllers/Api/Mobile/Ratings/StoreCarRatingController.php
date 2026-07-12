<?php

namespace App\Http\Controllers\Api\Mobile\Ratings;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Mobile\RatingRequest;
use App\Http\Resources\RatingResource;
use App\Models\Car;
use App\Services\RatingService;

/** Creates or updates the authenticated user's rating for a car. */
class StoreCarRatingController extends BaseApiController
{
    public function __construct(private readonly RatingService $ratingService) {}

    /** Persists the rating (one per user per car) and returns it. */
    public function __invoke(RatingRequest $request, Car $car)
    {
        $rating = $this->ratingService->addOrUpdate(
            $request->user(),
            $car,
            (int) $request->input('rating'),
            $request->input('comment')
        );

        return $this->success(new RatingResource($rating->load('user')), __('messages.ratings.saved'));
    }
}

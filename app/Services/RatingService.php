<?php

namespace App\Services;

use App\Models\Car;
use App\Models\CarRating;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles adding/updating car ratings and keeping the car's rating average in sync. */
class RatingService
{
    public function __construct(private readonly CarService $carService) {}

    /** Creates or updates the user's rating for the given car, then recalculates the car's rating average. */
    public function addOrUpdate(User $user, Car $car, int $rating, ?string $comment = null): CarRating
    {
        $carRating = CarRating::updateOrCreate(
            ['user_id' => $user->id, 'car_id' => $car->id],
            ['rating' => $rating, 'comment' => $comment]
        );

        $this->carService->updateRatingAvg($car);

        return $carRating;
    }

    /** Returns a paginated list of ratings for the given car, most recent first (ties broken by id). */
    public function list(Car $car, int $perPage = 15): LengthAwarePaginator
    {
        return $car->ratings()->with('user')->latest()->latest('id')->paginate($perPage);
    }
}

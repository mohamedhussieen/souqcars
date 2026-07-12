<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles favoriting/unfavoriting cars and listing a user's favorites, keeping Car.favorites_count in sync. */
class FavoriteService
{
    /** Toggles the favorite state of the given car for the given user; returns ['added' => bool]. */
    public function toggle(User $user, Car $car): array
    {
        $favorite = Favorite::where('user_id', $user->id)->where('car_id', $car->id)->first();

        if ($favorite) {
            $favorite->delete();
            $car->decrement('favorites_count');

            return ['added' => false];
        }

        Favorite::create(['user_id' => $user->id, 'car_id' => $car->id]);
        $car->increment('favorites_count');

        return ['added' => true];
    }

    /** Returns a paginated list of the user's favorited cars (with car relation eager-loaded). */
    public function list(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Favorite::query()
            ->where('user_id', $user->id)
            ->with(['car.brand', 'car.city', 'car.color'])
            ->latest()
            ->paginate($perPage);
    }
}

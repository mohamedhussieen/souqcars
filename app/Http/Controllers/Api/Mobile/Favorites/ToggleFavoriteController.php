<?php

namespace App\Http\Controllers\Api\Mobile\Favorites;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Car;
use App\Services\FavoriteService;
use Illuminate\Http\Request;

/** Toggles the authenticated user's favorite state for a car. */
class ToggleFavoriteController extends BaseApiController
{
    public function __construct(private readonly FavoriteService $favoriteService) {}

    /** Adds the car to favorites if absent, removes it if present; returns the resulting state. */
    public function __invoke(Request $request, Car $car)
    {
        $result = $this->favoriteService->toggle($request->user(), $car);

        return $this->success(
            $result,
            __($result['added'] ? 'messages.favorites.added' : 'messages.favorites.removed')
        );
    }
}

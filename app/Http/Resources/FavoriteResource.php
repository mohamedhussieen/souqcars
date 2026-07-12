<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a Favorite model into the mobile API response by delegating entirely
 * to CarListResource for its favorited car — the favorites list has the same card
 * shape as any other car list, so no duplicate field mapping is kept here.
 */
class FavoriteResource extends JsonResource
{
    /** Returns the favorited car's list-card representation, plus the favorite's own id/created_at. */
    public function toArray(Request $request): array
    {
        return array_merge(
            (new CarListResource($this->car))->toArray($request),
            [
                'favorite_id'  => $this->id,
                'favorited_at' => $this->created_at?->toISOString(),
            ]
        );
    }
}

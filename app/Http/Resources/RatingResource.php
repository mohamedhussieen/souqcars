<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a CarRating model into a mobile API response. */
class RatingResource extends JsonResource
{
    /** Returns the rating with the author's name and a human-readable timestamp. */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'user_name'         => $this->user?->name,
            'rating'            => $this->rating,
            'comment'           => $this->comment,
            'created_at_human'  => $this->created_at?->diffForHumans(),
        ];
    }
}

<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a City model into the admin dashboard representation with both languages. */
class AdminCityResource extends JsonResource
{
    /** Returns the city with its bilingual name fields. */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name_ar'    => $this->name_ar,
            'name_en'    => $this->name_en,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

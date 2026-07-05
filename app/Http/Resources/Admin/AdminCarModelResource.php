<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a CarModel model into the admin dashboard representation with both languages. */
class AdminCarModelResource extends JsonResource
{
    /** Returns the car model with its bilingual name fields and parent brand id. */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'brand_id'   => $this->brand_id,
            'name_ar'    => $this->name_ar,
            'name_en'    => $this->name_en,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

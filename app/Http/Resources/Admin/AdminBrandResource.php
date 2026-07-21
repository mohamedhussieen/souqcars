<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Brand model into the admin dashboard representation with both languages. */
class AdminBrandResource extends JsonResource
{
    /** Returns the brand with its bilingual name fields and logo URL. */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name_ar'      => $this->name_ar,
            'name_en'      => $this->name_en,
            'logo_url'     => $this->logo_url,
            'cars_count'   => $this->whenCounted('cars', fn () => $this->cars_count) ?? $this->cars()->count(),
            'models_count' => $this->whenCounted('carModels', fn () => $this->car_models_count) ?? $this->carModels()->count(),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}

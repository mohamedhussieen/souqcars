<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a CarWatchRequest model into a locale-aware mobile API response. */
class WatchRequestResource extends JsonResource
{
    /** Returns the watch request with localized brand/model names, active state, and last-notified time. */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'    => $this->id,
            'brand' => $this->whenLoaded('brand', fn () => [
                'id'   => $this->brand->id,
                'name' => $this->brand->{"name_{$locale}"} ?? $this->brand->name_ar,
            ]),
            'model' => $this->whenLoaded('carModel', fn () => [
                'id'   => $this->carModel->id,
                'name' => $this->carModel->{"name_{$locale}"} ?? $this->carModel->name_ar,
            ]),
            'is_active'         => $this->is_active,
            'notified_at_human' => $this->notified_at?->diffForHumans(),
        ];
    }
}

<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a grouped brand/model watch-demand row into the admin dashboard representation. */
class WatchRequestOverviewResource extends JsonResource
{
    /** Returns the brand/model pair with its watcher count and latest request time. */
    public function toArray(Request $request): array
    {
        return [
            'brand_name'        => $this->brand?->name_en ?? $this->brand?->name_ar,
            'model_name'        => $this->carModel?->name_en ?? $this->carModel?->name_ar,
            'watchers_count'    => $this->watchers_count,
            'latest_request_at' => $this->latest_request_at,
        ];
    }
}

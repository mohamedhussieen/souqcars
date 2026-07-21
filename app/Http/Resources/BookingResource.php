<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Booking model into a locale-aware mobile API response. */
class BookingResource extends JsonResource
{
    /** Returns the booking with its status, schedule, price, and related service/center/car summaries. */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'                  => $this->id,
            'status'              => $this->status->value,
            'date'                => $this->date?->toDateString(),
            'time'                => $this->time,
            'price'               => (float) $this->price,
            'notes'               => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'service'             => $this->whenLoaded('maintenanceService', fn () => [
                'name'        => $this->maintenanceService->{"name_{$locale}"} ?? $this->maintenanceService->name_ar,
                'center_name' => $this->maintenanceCenter->{"name_{$locale}"} ?? $this->maintenanceCenter->name_ar,
            ]),
            'car'                 => $this->whenLoaded('car', fn () => $this->car ? [
                'title'     => $this->car->{"title_{$locale}"} ?? $this->car->title_ar,
                'thumb_url' => $this->car->thumb_url,
            ] : null),
            'created_at_human'    => $this->created_at?->diffForHumans(),
        ];
    }
}

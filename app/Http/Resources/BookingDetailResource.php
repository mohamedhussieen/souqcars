<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Booking model with full center details into a locale-aware mobile detail response. */
class BookingDetailResource extends JsonResource
{
    /** Returns the booking's full detail including the maintenance center's contact info. */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $base = (new BookingResource($this->resource))->toArray($request);

        return array_merge($base, [
            'center' => $this->whenLoaded('maintenanceCenter', fn () => [
                'name'     => $this->maintenanceCenter->{"name_{$locale}"} ?? $this->maintenanceCenter->name_ar,
                'phone'    => $this->maintenanceCenter->phone,
                'whatsapp' => $this->maintenanceCenter->whatsapp,
                'address'  => $this->maintenanceCenter->{"address_{$locale}"} ?? $this->maintenanceCenter->address_ar,
                'logo_url' => $this->maintenanceCenter->logo_url,
            ]),
        ]);
    }
}

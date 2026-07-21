<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a MaintenanceCenter model into the admin dashboard representation with both languages. */
class MaintenanceCenterAdminResource extends JsonResource
{
    /** Returns the center with its bilingual fields, active state, logo, and services in both languages. */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name_ar'    => $this->name_ar,
            'name_en'    => $this->name_en,
            'phone'      => $this->phone,
            'whatsapp'   => $this->whatsapp,
            'address_ar' => $this->address_ar,
            'address_en' => $this->address_en,
            'lat'        => $this->lat !== null ? (float) $this->lat : null,
            'lng'        => $this->lng !== null ? (float) $this->lng : null,
            'rating'     => (float) $this->rating,
            'is_active'  => $this->is_active,
            'logo_url'   => $this->logo_url,
            'services'   => MaintenanceServiceAdminResource::collection($this->whenLoaded('services')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

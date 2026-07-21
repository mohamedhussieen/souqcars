<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a MaintenanceCenter model into a locale-aware mobile API response. */
class MaintenanceCenterResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the center with localized name/address, rating, logo, and its active-services count. */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->localizeField('name'),
            'phone'          => $this->phone,
            'whatsapp'       => $this->whatsapp,
            'address'        => $this->localizeField('address'),
            'rating'         => (float) $this->rating,
            'is_active'      => $this->is_active,
            'logo_url'       => $this->logo_url,
            'services_count' => $this->whenCounted('services', fn () => $this->services_count) ?? $this->services()->where('is_active', true)->count(),
        ];
    }
}

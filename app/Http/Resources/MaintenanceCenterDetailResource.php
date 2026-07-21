<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a MaintenanceCenter model with its services into a locale-aware mobile detail response. */
class MaintenanceCenterDetailResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the center's full detail including its active services list. */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->localizeField('name'),
            'phone'     => $this->phone,
            'whatsapp'  => $this->whatsapp,
            'address'   => $this->localizeField('address'),
            'rating'    => (float) $this->rating,
            'is_active' => $this->is_active,
            'logo_url'  => $this->logo_url,
            'services'  => MaintenanceServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a MaintenanceService model into a locale-aware mobile API response. */
class MaintenanceServiceResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the service with localized name/description, price, and sort order. */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->localizeField('name'),
            'description' => $this->localizeField('description'),
            'price'       => (float) $this->price,
            'sort_order'  => $this->sort_order,
        ];
    }
}

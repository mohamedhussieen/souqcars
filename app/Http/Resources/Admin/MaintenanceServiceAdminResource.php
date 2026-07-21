<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a MaintenanceService model into the admin dashboard representation with both languages. */
class MaintenanceServiceAdminResource extends JsonResource
{
    /** Returns the service with its bilingual fields, price, and sort order. */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name_ar'         => $this->name_ar,
            'name_en'         => $this->name_en,
            'description_ar'  => $this->description_ar,
            'description_en'  => $this->description_en,
            'price'           => (float) $this->price,
            'is_active'       => $this->is_active,
            'sort_order'      => $this->sort_order,
        ];
    }
}

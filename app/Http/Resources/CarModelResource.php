<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a CarModel model into a locale-aware API response. */
class CarModelResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns car model with a single localized 'name' field and its brand_id. */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'brand_id' => $this->brand_id,
            'name'     => $this->localizeField('name'),
        ];
    }
}

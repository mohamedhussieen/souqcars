<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Color model into a locale-aware API response. */
class ColorResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns color with a single localized 'name' field resolved from the current app locale. */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->localizeField('name'),
        ];
    }
}

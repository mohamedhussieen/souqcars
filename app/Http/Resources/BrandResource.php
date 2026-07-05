<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Brand model into a locale-aware API response with logo URL. */
class BrandResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns brand with a single localized 'name' field and optional logo URL. */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->localizeField('name'),
            'logo_url' => $this->logo_url,
        ];
    }
}

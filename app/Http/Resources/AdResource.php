<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms an Ad model into a locale-aware mobile API response. */
class AdResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the ad with a single localized 'title' field and its navigation/image data. */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->localizeField('title'),
            'type'         => $this->type?->value,
            'action_type'  => $this->action_type?->value,
            'action_value' => $this->action_value,
            'image_url'    => $this->image_url,
            'sort_order'   => $this->sort_order,
        ];
    }
}

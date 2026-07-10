<?php

namespace App\Http\Resources;

use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a PolicyTerm model into a locale-aware API response. */
class PolicyTermResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the policy term with 'title'/'body' localized from the current app locale. */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'order' => $this->order,
            'title' => $this->localizeField('title'),
            'body'  => $this->localizeField('body'),
        ];
    }
}

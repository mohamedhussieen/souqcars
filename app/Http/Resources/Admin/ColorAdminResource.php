<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Color model into the admin dashboard representation with both languages. */
class ColorAdminResource extends JsonResource
{
    /** Returns the color with its bilingual name fields and active flag. */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name_ar'   => $this->name_ar,
            'name_en'   => $this->name_en,
            'is_active' => $this->is_active,
        ];
    }
}

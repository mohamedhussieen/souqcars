<?php

namespace App\Http\Resources;

use App\Enums\SellerType;
use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Showroom model into a locale-aware mobile API response. */
class ShowroomResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the showroom with localized name/address, rating, verification, logo, and its car count. */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->localizeField('name'),
            'phone'       => $this->phone,
            'whatsapp'    => $this->whatsapp,
            'address'     => $this->localizeField('address'),
            'rating'      => (float) $this->rating,
            'is_verified' => $this->is_verified,
            'logo_url'    => $this->logo_url,
            'cars_count'  => $this->whenCounted('cars', fn () => $this->cars_count) ?? $this->cars()->where('status', 'active')->count(),
        ];
    }
}

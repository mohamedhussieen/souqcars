<?php

namespace App\Http\Resources\Admin;

use App\Enums\SellerType;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a User model into the admin dashboard representation. */
class UserAdminResource extends JsonResource
{
    /** Returns the user with role names, active state, and listing/favorite counts. */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'is_active'       => $this->is_active,
            'roles'           => $this->getRoleNames(),
            'listings_count'  => Car::query()->where('seller_type', SellerType::Individual)->where('seller_id', $this->id)->count(),
            'favorites_count' => $this->favorites()->count(),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}

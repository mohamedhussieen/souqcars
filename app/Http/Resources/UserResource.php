<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a User model into the API response representation. */
class UserResource extends JsonResource
{
    /** Returns the user's public-facing data including avatar URL and assigned role. */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'is_active'            => $this->is_active,
            'notification_enabled' => $this->notification_enabled,
            'theme'                => $this->theme?->value,
            'avatar_url'           => $this->avatar_url,
            'policy_accepted'      => $this->hasAcceptedPolicy(),
            'roles'                => $this->getRoleNames(),
            'created_at'           => $this->created_at?->toISOString(),
        ];
    }
}

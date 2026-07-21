<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Notification model into a locale-aware mobile API response. */
class NotificationResource extends JsonResource
{
    /** Returns the notification with localized title/body, its payload, read state, and a human timestamp. */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'               => $this->id,
            'type'             => $this->type->value,
            'title'            => $this->{"title_{$locale}"} ?? $this->title_ar,
            'body'             => $this->{"body_{$locale}"} ?? $this->body_ar,
            'data'             => $this->data,
            'is_read'          => $this->read_at !== null,
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}

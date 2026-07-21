<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Booking model into the admin dashboard representation with both languages. */
class BookingAdminResource extends JsonResource
{
    /** Returns the booking's full detail including the user, service, center, and car in both languages. */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'status'              => $this->status->value,
            'date'                => $this->date?->toDateString(),
            'time'                => $this->time,
            'price'               => (float) $this->price,
            'notes'               => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'user'                => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'phone' => $this->user->phone,
                'email' => $this->user->email,
            ]),
            'service' => $this->whenLoaded('maintenanceService', fn () => [
                'id'      => $this->maintenanceService->id,
                'name_ar' => $this->maintenanceService->name_ar,
                'name_en' => $this->maintenanceService->name_en,
            ]),
            'center' => $this->whenLoaded('maintenanceCenter', fn () => [
                'id'      => $this->maintenanceCenter->id,
                'name_ar' => $this->maintenanceCenter->name_ar,
                'name_en' => $this->maintenanceCenter->name_en,
            ]),
            'car' => $this->whenLoaded('car', fn () => $this->car ? [
                'id'      => $this->car->id,
                'title_ar' => $this->car->title_ar,
                'title_en' => $this->car->title_en,
            ] : null),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

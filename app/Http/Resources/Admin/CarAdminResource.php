<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Car model into the admin dashboard representation with both languages and all raw fields. */
class CarAdminResource extends JsonResource
{
    /** Returns every car field bilingually, plus media URLs, for dashboard editing/review. */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'seller_type'           => $this->seller_type?->value,
            'seller_id'             => $this->seller_id,
            'brand_id'              => $this->brand_id,
            'car_model_id'          => $this->car_model_id,
            'city_id'               => $this->city_id,
            'color_id'              => $this->color_id,
            'year'                  => $this->year,
            'title_ar'              => $this->title_ar,
            'title_en'              => $this->title_en,
            'description_ar'        => $this->description_ar,
            'description_en'        => $this->description_en,
            'price'                 => (float) $this->price,
            'payment_type'          => $this->payment_type?->value,
            'mileage'               => $this->mileage,
            'condition'             => $this->condition?->value,
            'transmission'          => $this->transmission?->value,
            'fuel_type'             => $this->fuel_type?->value,
            'body_type'             => $this->body_type?->value,
            'owners_count'          => $this->owners_count,
            'has_inspection_report' => $this->has_inspection_report,
            'inspection_report_url' => $this->inspection_report_url,
            'status'                => $this->status?->value,
            'rejection_reason'      => $this->rejection_reason,
            'is_featured'           => $this->is_featured,
            'views_count'           => $this->views_count,
            'favorites_count'       => $this->favorites_count,
            'rating_avg'            => (float) $this->rating_avg,
            'gallery'               => $this->getMedia('car_images')->map(fn ($media) => [
                'id'        => $media->id,
                'thumb_url' => $media->getUrl('thumb'),
                'full_url'  => $media->getUrl('full'),
            ])->values()->all(),
            'created_at'            => $this->created_at?->toISOString(),
            'updated_at'            => $this->updated_at?->toISOString(),
        ];
    }
}

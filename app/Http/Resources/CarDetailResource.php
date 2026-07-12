<?php

namespace App\Http\Resources;

use App\Enums\SellerType;
use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Car model into the full locale-aware mobile detail-screen API response. */
class CarDetailResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the full car detail payload including gallery, seller, and recent ratings. */
    public function toArray(Request $request): array
    {
        $list = (new CarListResource($this->resource))->toArray($request);

        return array_merge($list, [
            'description'           => $this->localizeField('description'),
            'owners_count'          => $this->owners_count,
            'gallery'               => $this->getMedia('car_images')->map(fn ($media) => [
                'id'        => $media->id,
                'thumb_url' => $media->getUrl('thumb'),
                'full_url'  => $media->getUrl('full'),
            ])->values()->all(),
            'inspection_report_url' => $this->inspection_report_url,
            'seller'                => $this->resolveSeller(),
            'ratings_avg'           => (float) $this->rating_avg,
            'ratings_count'         => $this->ratings()->count(),
            'ratings'               => RatingResource::collection(
                $this->ratings()->latest()->limit(3)->get()
            ),
        ]);
    }

    /** Resolves the seller object (type, name, phone, whatsapp, rating, is_verified, logo_url). */
    protected function resolveSeller(): ?array
    {
        $seller = $this->seller();

        if (!$seller) {
            return null;
        }

        if ($this->seller_type === SellerType::Showroom) {
            $locale = app()->getLocale();

            return [
                'type'        => $this->seller_type->value,
                'name'        => $seller->{"name_{$locale}"} ?? $seller->name_ar,
                'phone'       => $seller->phone,
                'whatsapp'    => $seller->whatsapp,
                'rating'      => (float) $seller->rating,
                'is_verified' => $seller->is_verified,
                'logo_url'    => $seller->logo_url,
            ];
        }

        return [
            'type'        => $this->seller_type->value,
            'name'        => $seller->name,
            'phone'       => $seller->phone,
            'whatsapp'    => null,
            'rating'      => null,
            'is_verified' => false,
            'logo_url'    => $seller->avatar_url ?? null,
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Enums\SellerType;
use App\Traits\HasLocalizedFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Transforms a Car model into a locale-aware mobile list-card API response. */
class CarListResource extends JsonResource
{
    use HasLocalizedFields;

    /** Returns the summarized car-card fields shown in listing screens. */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'title'                 => $this->localizeField('title'),
            'price'                 => (float) $this->price,
            'payment_type'          => $this->payment_type?->value,
            'year'                  => $this->year,
            'mileage'               => $this->mileage,
            'transmission'          => $this->transmission?->value,
            'fuel_type'             => $this->fuel_type?->value,
            'body_type'             => $this->body_type?->value,
            'condition'             => $this->condition?->value,
            'status'                => $this->status?->value,
            'is_featured'           => $this->is_featured,
            'city'                  => $this->whenLoaded('city', fn () => $this->localizeRelation($this->city, 'name')),
            'brand'                 => $this->whenLoaded('brand', fn () => $this->localizeRelation($this->brand, 'name')),
            'color'                 => $this->whenLoaded('color', fn () => $this->color ? $this->localizeRelation($this->color, 'name') : null),
            'has_inspection_report' => $this->has_inspection_report,
            'seller_type'           => $this->seller_type?->value,
            'seller_name'           => $this->resolveSellerName(),
            'thumb_url'             => $this->thumb_url,
            'favorites_count'       => $this->favorites_count,
            'views_count'           => $this->views_count,
            'is_favorited'          => $this->resolveIsFavorited($request),
        ];
    }

    /** Resolves a bilingual name field on an already-loaded related model without a JsonResource wrapper. */
    protected function localizeRelation(mixed $related, string $field): ?string
    {
        if (!$related) {
            return null;
        }

        $locale = app()->getLocale();
        $column = "{$field}_{$locale}";

        return $related->{$column} ?? $related->{$field . '_ar'} ?? null;
    }

    /** Returns the seller's display name, resolved from the polymorphic-ish seller() accessor. */
    protected function resolveSellerName(): ?string
    {
        $seller = $this->seller();

        if (!$seller) {
            return null;
        }

        return $this->seller_type === SellerType::Showroom
            ? ($seller->name_ar ?? null)
            : ($seller->name ?? null);
    }

    /**
     * Returns whether the authenticated user has favorited this car. Relies on the
     * 'favoritedByUser' relation being eager-loaded (constrained to the current user)
     * by the caller to avoid N+1 queries; falls back to false for guests.
     */
    protected function resolveIsFavorited(Request $request): bool
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('favoritedByUser')) {
            return $this->favoritedByUser->isNotEmpty();
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}

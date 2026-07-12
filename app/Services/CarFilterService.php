<?php

namespace App\Services;

use App\Enums\SortBy;
use Illuminate\Database\Eloquent\Builder;

/**
 * Applies optional query filters to a Car query builder. Never scopes to status itself —
 * callers (mobile controllers scope to active, admin doesn't) are responsible for that.
 */
class CarFilterService
{
    /** Applies each provided filter to the query via ->when(), skipping any null/empty value. */
    public function apply(Builder $query, array $filters): Builder
    {
        $query
            ->when($filters['brand_id'] ?? null, fn (Builder $q, $v) => $q->where('brand_id', $v))
            ->when($filters['car_model_id'] ?? null, fn (Builder $q, $v) => $q->where('car_model_id', $v))
            ->when($filters['city_id'] ?? null, fn (Builder $q, $v) => $q->where('city_id', $v))
            ->when($filters['color_id'] ?? null, fn (Builder $q, $v) => $q->where('color_id', $v))
            ->when($filters['year_from'] ?? null, fn (Builder $q, $v) => $q->where('year', '>=', $v))
            ->when($filters['year_to'] ?? null, fn (Builder $q, $v) => $q->where('year', '<=', $v))
            ->when($filters['price_min'] ?? null, fn (Builder $q, $v) => $q->where('price', '>=', $v))
            ->when($filters['price_max'] ?? null, fn (Builder $q, $v) => $q->where('price', '<=', $v))
            ->when($filters['mileage_max'] ?? null, fn (Builder $q, $v) => $q->where('mileage', '<=', $v))
            ->when($filters['condition'] ?? null, fn (Builder $q, $v) => $q->where('condition', $v))
            ->when($filters['transmission'] ?? null, fn (Builder $q, $v) => $q->where('transmission', $v))
            ->when($filters['fuel_type'] ?? null, fn (Builder $q, $v) => $q->where('fuel_type', $v))
            ->when($filters['body_type'] ?? null, fn (Builder $q, $v) => $q->where('body_type', $v))
            ->when($filters['payment_type'] ?? null, fn (Builder $q, $v) => $q->where('payment_type', $v))
            ->when($filters['has_inspection'] ?? null, fn (Builder $q, $v) => $q->where('has_inspection_report', true))
            ->when($filters['seller_type'] ?? null, fn (Builder $q, $v) => $q->where('seller_type', $v))
            ->when($filters['search'] ?? null, fn (Builder $q, $v) => $q->where(
                fn (Builder $sub) => $sub->where('title_ar', 'like', "%{$v}%")
                    ->orWhere('title_en', 'like', "%{$v}%")
            ));

        $this->applySort($query, $filters['sort_by'] ?? null);

        return $query;
    }

    /** Applies the requested sort order, defaulting to newest-first. */
    private function applySort(Builder $query, ?string $sortBy): void
    {
        $sort = SortBy::tryFrom((string) $sortBy) ?? SortBy::Newest;

        match ($sort) {
            SortBy::Newest => $query->orderByDesc('created_at'),
            SortBy::Oldest => $query->orderBy('created_at'),
            SortBy::PriceAsc => $query->orderBy('price'),
            SortBy::PriceDesc => $query->orderByDesc('price'),
            SortBy::MileageAsc => $query->orderBy('mileage'),
        };
    }
}

<?php

namespace App\Services;

use App\Enums\CarStatus;
use App\Enums\SellerType;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Showroom;
use App\Models\User;

/** Builds the aggregated payload for the mobile home screen. */
class HomeService
{
    public function __construct(private readonly AdService $adService) {}

    /** Returns ads, brands, customer/latest/featured cars, and showrooms for the home screen. */
    public function build(?User $user = null): array
    {
        return [
            'ads' => $this->adService->activeAds()->take(10)->values(),
            'brands' => Brand::query()
                ->withCount('cars')
                ->groupBy('brands.id')
                ->having('cars_count', '>', 0)
                ->limit(10)
                ->get(),
            'customer_cars' => $this->carsWith($user)
                ->where('seller_type', SellerType::Individual)
                ->where('status', CarStatus::Active)
                ->latest()
                ->limit(10)
                ->get(),
            'latest_cars' => $this->carsWith($user)
                ->where('status', CarStatus::Active)
                ->latest()
                ->limit(10)
                ->get(),
            'showrooms' => Showroom::query()
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->limit(6)
                ->get(),
            'featured_cars' => $this->carsWith($user)
                ->where('is_featured', true)
                ->where('status', CarStatus::Active)
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }

    /** Returns a base Car query eager-loaded with lookup relations and the current user's favorite state. */
    private function carsWith(?User $user)
    {
        return Car::query()->with(['brand', 'city', 'color', 'favoritedByUser' => fn ($q) => $q->where('user_id', $user?->id)]);
    }
}

<?php

namespace App\Services;

use App\Enums\SellerType;
use App\Models\Car;
use App\Models\User;

/** Computes summary stats for a user's profile: favorites, own-listing views, and listing count. */
class ProfileStatsService
{
    /** Returns favorites_count, views_count (summed across own listings), and listings_count. */
    public function stats(User $user): array
    {
        $ownListings = Car::query()
            ->where('seller_type', SellerType::Individual)
            ->where('seller_id', $user->id);

        return [
            'favorites_count' => $user->favorites()->count(),
            'views_count'     => (clone $ownListings)->sum('views_count'),
            'listings_count'  => (clone $ownListings)->count(),
        ];
    }
}

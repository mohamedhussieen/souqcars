<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CarStatus;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\City;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Support\Collection;

/** Computes admin-dashboard summary stats and analytics breakdowns. */
class DashboardStatsService
{
    /** Returns headline counts for the dashboard overview. */
    public function stats(): array
    {
        return [
            'total_cars'          => Car::query()->count(),
            'active_cars'         => Car::query()->where('status', CarStatus::Active)->count(),
            'pending_cars'        => Car::query()->where('status', CarStatus::Pending)->count(),
            'total_users'         => User::query()->count(),
            'active_users'        => User::query()->where('is_active', true)->count(),
            'total_bookings'      => Booking::query()->count(),
            'pending_bookings'    => Booking::query()->where('status', BookingStatus::Pending)->count(),
            'total_showrooms'     => Showroom::query()->count(),
            'cars_this_month'     => Car::query()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'bookings_this_month' => Booking::query()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];
    }

    /** Returns car listing counts for each of the last 12 months. */
    public function carsPerMonth(): Collection
    {
        $months = collect(range(0, 11))->map(fn ($i) => now()->subMonths(11 - $i)->format('Y-m'));

        $counts = Car::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->pluck('count', 'month');

        return $months->map(fn ($month) => [
            'month' => $month,
            'count' => (int) ($counts[$month] ?? 0),
        ]);
    }

    /** Returns booking counts grouped by status. */
    public function bookingsPerStatus(): array
    {
        $counts = Booking::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'pending'   => (int) ($counts['pending'] ?? 0),
            'confirmed' => (int) ($counts['confirmed'] ?? 0),
            'completed' => (int) ($counts['completed'] ?? 0),
            'cancelled' => (int) ($counts['cancelled'] ?? 0),
        ];
    }

    /** Returns the top 5 brands by car count. */
    public function topBrands(): Collection
    {
        return Brand::query()
            ->withCount('cars')
            ->orderByDesc('cars_count')
            ->limit(5)
            ->get()
            ->map(fn (Brand $brand) => [
                'brand_name' => $brand->name_en ?? $brand->name_ar,
                'count'      => $brand->cars_count,
            ]);
    }

    /** Returns the top 5 cities by car count. */
    public function topCities(): Collection
    {
        return City::query()
            ->withCount('cars')
            ->orderByDesc('cars_count')
            ->limit(5)
            ->get()
            ->map(fn (City $city) => [
                'city_name' => $city->name_en ?? $city->name_ar,
                'count'     => $city->cars_count,
            ]);
    }
}

<?php

namespace Tests\Unit;

use App\Enums\SellerType;
use App\Models\Car;
use App\Models\Favorite;
use App\Models\User;
use App\Services\ProfileStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies ProfileStatsService aggregates favorites, own-listing views, and listing counts correctly. */
class ProfileStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProfileStatsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProfileStatsService();
    }

    public function test_stats_aggregates_favorites_views_and_listings(): void
    {
        $user = User::factory()->create();
        $otherCar = Car::factory()->create();
        Favorite::create(['user_id' => $user->id, 'car_id' => $otherCar->id]);

        Car::factory()->create([
            'seller_type' => SellerType::Individual,
            'seller_id'   => $user->id,
            'views_count' => 10,
        ]);
        Car::factory()->create([
            'seller_type' => SellerType::Individual,
            'seller_id'   => $user->id,
            'views_count' => 5,
        ]);

        $stats = $this->service->stats($user);

        $this->assertSame(1, $stats['favorites_count']);
        $this->assertSame(15, $stats['views_count']);
        $this->assertSame(2, $stats['listings_count']);
    }
}

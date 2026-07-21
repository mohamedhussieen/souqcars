<?php

namespace Tests\Unit;

use App\Enums\CarStatus;
use App\Enums\SellerType;
use App\Models\Ad;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Showroom;
use App\Services\AdService;
use App\Services\HomeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies HomeService aggregates ads, brands, cars, and showrooms for the mobile home screen. */
class HomeServiceTest extends TestCase
{
    use RefreshDatabase;

    private HomeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HomeService(new AdService());
    }

    public function test_build_includes_only_brands_with_cars(): void
    {
        $brandWithCars = Brand::factory()->create();
        Car::factory()->create(['brand_id' => $brandWithCars->id]);
        Brand::factory()->create();

        $result = $this->service->build();

        $this->assertCount(1, $result['brands']);
        $this->assertSame($brandWithCars->id, $result['brands']->first()->id);
    }

    public function test_build_customer_cars_only_includes_active_individual_listings(): void
    {
        $match = Car::factory()->create(['seller_type' => SellerType::Individual, 'status' => CarStatus::Active]);
        Car::factory()->create(['seller_type' => SellerType::Admin, 'status' => CarStatus::Active]);
        Car::factory()->create(['seller_type' => SellerType::Individual, 'status' => CarStatus::Sold]);

        $result = $this->service->build();

        $this->assertCount(1, $result['customer_cars']);
        $this->assertTrue($result['customer_cars']->first()->is($match));
    }

    public function test_build_latest_cars_only_includes_active_listings(): void
    {
        $active = Car::factory()->create(['status' => CarStatus::Active]);
        Car::factory()->create(['status' => CarStatus::Sold]);

        $result = $this->service->build();

        $this->assertCount(1, $result['latest_cars']);
        $this->assertTrue($result['latest_cars']->first()->is($active));
    }

    public function test_build_featured_cars_only_includes_active_featured_listings(): void
    {
        $featured = Car::factory()->create(['is_featured' => true, 'status' => CarStatus::Active]);
        Car::factory()->create(['is_featured' => false, 'status' => CarStatus::Active]);
        Car::factory()->create(['is_featured' => true, 'status' => CarStatus::Sold]);

        $result = $this->service->build();

        $this->assertCount(1, $result['featured_cars']);
        $this->assertTrue($result['featured_cars']->first()->is($featured));
    }

    public function test_build_showrooms_only_includes_active_ones_ordered_by_rating(): void
    {
        $lowRated = Showroom::factory()->create(['is_active' => true, 'rating' => 2.0]);
        $highRated = Showroom::factory()->create(['is_active' => true, 'rating' => 4.5]);
        Showroom::factory()->create(['is_active' => false, 'rating' => 5.0]);

        $result = $this->service->build();

        $this->assertCount(2, $result['showrooms']);
        $this->assertTrue($result['showrooms']->first()->is($highRated));
        $this->assertTrue($result['showrooms']->last()->is($lowRated));
    }

    public function test_build_includes_active_ads_ordered_by_sort_order(): void
    {
        Ad::create(['title_ar' => 'ب', 'title_en' => 'B', 'type' => 'banner', 'is_active' => true, 'sort_order' => 2]);
        Ad::create(['title_ar' => 'أ', 'title_en' => 'A', 'type' => 'banner', 'is_active' => true, 'sort_order' => 1]);
        Ad::create(['title_ar' => 'مخفي', 'title_en' => 'Hidden', 'type' => 'banner', 'is_active' => false, 'sort_order' => 0]);

        $result = $this->service->build();

        $this->assertCount(2, $result['ads']);
        $this->assertSame('A', $result['ads']->first()->title_en);
    }
}

<?php

namespace Tests\Feature\Mobile;

use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the public mobile car listing, search, and detail endpoints. */
class CarsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_cars_returns_only_active_listings(): void
    {
        Car::factory()->create(['status' => CarStatus::Active]);
        Car::factory()->create(['status' => CarStatus::Sold]);

        $response = $this->getJson('/api/v1/mobile/cars');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_list_cars_filters_by_brand_id(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Active]);
        Car::factory()->create(['status' => CarStatus::Active]);

        $response = $this->getJson('/api/v1/mobile/cars?brand_id=' . $car->brand_id);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_list_cars_validation_rejects_invalid_brand_id(): void
    {
        $response = $this->getJson('/api/v1/mobile/cars?brand_id=999999');

        $response->assertStatus(422);
    }

    public function test_show_car_returns_active_car_details_and_increments_views(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Active, 'views_count' => 0]);

        $response = $this->getJson("/api/v1/mobile/cars/{$car->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $car->id);
        $this->assertSame(1, $car->fresh()->views_count);
    }

    public function test_show_car_returns_404_for_non_active_car(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Pending]);

        $response = $this->getJson("/api/v1/mobile/cars/{$car->id}");

        $response->assertStatus(404);
    }

    public function test_show_car_returns_404_for_missing_car(): void
    {
        $response = $this->getJson('/api/v1/mobile/cars/999999');

        $response->assertStatus(404);
    }

    public function test_search_cars_finds_matching_active_cars(): void
    {
        Car::factory()->create(['status' => CarStatus::Active, 'title_en' => 'Toyota Camry 2020']);
        Car::factory()->create(['status' => CarStatus::Active, 'title_en' => 'Hyundai Elantra']);

        $response = $this->getJson('/api/v1/mobile/search?search=Camry');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_search_cars_requires_a_search_term(): void
    {
        $response = $this->getJson('/api/v1/mobile/search');

        $response->assertStatus(422)->assertJsonValidationErrors(['search']);
    }

    public function test_search_cars_rejects_too_short_search_term(): void
    {
        $response = $this->getJson('/api/v1/mobile/search?search=a');

        $response->assertStatus(422)->assertJsonValidationErrors(['search']);
    }
}

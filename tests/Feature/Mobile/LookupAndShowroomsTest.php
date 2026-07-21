<?php

namespace Tests\Feature\Mobile;

use App\Enums\CarStatus;
use App\Enums\SellerType;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\City;
use App\Models\Color;
use App\Models\Showroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the public mobile lookup endpoints (cities/brands/models/colors) and showroom endpoints. */
class LookupAndShowroomsTest extends TestCase
{
    use RefreshDatabase;

    public function test_lookup_cities_returns_paginated_cities(): void
    {
        City::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/mobile/lookup/cities');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_lookup_cities_validation_rejects_per_page_over_fifty(): void
    {
        $response = $this->getJson('/api/v1/mobile/lookup/cities?per_page=999');

        $response->assertStatus(422)->assertJsonValidationErrors(['per_page']);
    }

    public function test_lookup_cities_defaults_per_page_to_fifteen_when_omitted(): void
    {
        City::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/mobile/lookup/cities');

        $response->assertStatus(200)->assertJsonPath('meta.per_page', 15);
        $this->assertCount(15, $response->json('data'));
    }

    public function test_lookup_cities_validation_rejects_zero_page(): void
    {
        $response = $this->getJson('/api/v1/mobile/lookup/cities?page=0');

        $response->assertStatus(422)->assertJsonValidationErrors(['page']);
    }

    public function test_lookup_cities_page_beyond_range_returns_empty_data(): void
    {
        City::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/mobile/lookup/cities?page=5');

        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    public function test_lookup_cities_localizes_name_by_accept_language_header(): void
    {
        City::factory()->create(['name_ar' => 'القاهرة', 'name_en' => 'Cairo']);

        $arResponse = $this->withHeader('Accept-Language', 'ar')->getJson('/api/v1/mobile/lookup/cities');
        $enResponse = $this->withHeader('Accept-Language', 'en')->getJson('/api/v1/mobile/lookup/cities');

        $this->assertSame('القاهرة', $arResponse->json('data.0.name'));
        $this->assertSame('Cairo', $enResponse->json('data.0.name'));
    }

    public function test_lookup_cities_defaults_to_arabic_without_accept_language_header(): void
    {
        City::factory()->create(['name_ar' => 'القاهرة', 'name_en' => 'Cairo']);

        $response = $this->getJson('/api/v1/mobile/lookup/cities');

        $this->assertSame('القاهرة', $response->json('data.0.name'));
    }

    public function test_lookup_brands_returns_paginated_brands(): void
    {
        Brand::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/mobile/lookup/brands');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_lookup_brand_models_returns_only_that_brands_models(): void
    {
        $brand = Brand::factory()->create();
        CarModel::factory()->create(['brand_id' => $brand->id]);
        CarModel::factory()->create();

        $response = $this->getJson("/api/v1/mobile/lookup/brands/{$brand->id}/models");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_lookup_colors_returns_only_active_colors(): void
    {
        Color::factory()->create(['is_active' => true]);
        Color::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/mobile/lookup/colors');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_list_showrooms_returns_only_active_showrooms(): void
    {
        Showroom::factory()->create(['is_active' => true]);
        Showroom::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/mobile/showrooms');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_show_showroom_returns_the_showroom_profile(): void
    {
        $showroom = Showroom::factory()->create();

        $response = $this->getJson("/api/v1/mobile/showrooms/{$showroom->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $showroom->id);
    }

    public function test_showroom_cars_returns_only_that_showrooms_active_cars(): void
    {
        $showroom = Showroom::factory()->create();
        $match = Car::factory()->create([
            'seller_type' => SellerType::Showroom,
            'seller_id'   => $showroom->id,
            'status'      => CarStatus::Active,
        ]);
        Car::factory()->create([
            'seller_type' => SellerType::Showroom,
            'seller_id'   => $showroom->id,
            'status'      => CarStatus::Sold,
        ]);
        Car::factory()->create(['status' => CarStatus::Active]);

        $response = $this->getJson("/api/v1/mobile/showrooms/{$showroom->id}/cars");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($match->id, $response->json('data.0.id'));
    }
}

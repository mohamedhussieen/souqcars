<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\City;
use App\Services\LookupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies LookupService paginated retrieval of cities, brands, and brand-scoped car models. */
class LookupServiceTest extends TestCase
{
    use RefreshDatabase;

    private LookupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LookupService();
    }

    public function test_get_cities_returns_paginated_cities(): void
    {
        City::factory()->count(3)->create();

        $result = $this->service->getCities(10);

        $this->assertSame(3, $result->total());
    }

    public function test_get_cities_respects_per_page(): void
    {
        City::factory()->count(5)->create();

        $result = $this->service->getCities(2);

        $this->assertCount(2, $result->items());
        $this->assertSame(5, $result->total());
    }

    public function test_get_brands_returns_paginated_brands(): void
    {
        Brand::factory()->count(4)->create();

        $result = $this->service->getBrands(10);

        $this->assertSame(4, $result->total());
    }

    public function test_get_models_by_brand_returns_only_that_brands_models(): void
    {
        $brand = Brand::factory()->create();
        $otherBrand = Brand::factory()->create();
        CarModel::factory()->count(2)->create(['brand_id' => $brand->id]);
        CarModel::factory()->create(['brand_id' => $otherBrand->id]);

        $result = $this->service->getModelsByBrand($brand, 10);

        $this->assertSame(2, $result->total());
    }
}

<?php

namespace Tests\Unit;

use App\Models\City;
use App\Services\AdminCityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies AdminCityService listing, creation, update, and deletion of cities. */
class AdminCityServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminCityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdminCityService();
    }

    public function test_list_returns_paginated_cities(): void
    {
        City::factory()->count(3)->create();

        $result = $this->service->list(10);

        $this->assertSame(3, $result->total());
    }

    public function test_create_persists_a_new_city(): void
    {
        $city = $this->service->create(['name_ar' => 'القاهرة', 'name_en' => 'Cairo']);

        $this->assertDatabaseHas('cities', ['name_en' => 'Cairo']);
        $this->assertSame('Cairo', $city->name_en);
    }

    public function test_update_changes_city_names(): void
    {
        $city = City::factory()->create(['name_en' => 'Old']);

        $updated = $this->service->update($city, ['name_ar' => $city->name_ar, 'name_en' => 'New']);

        $this->assertSame('New', $updated->name_en);
    }

    public function test_delete_removes_the_city(): void
    {
        $city = City::factory()->create();

        $this->service->delete($city);

        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }
}

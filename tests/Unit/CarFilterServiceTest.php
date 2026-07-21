<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\City;
use App\Models\Color;
use App\Services\CarFilterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies CarFilterService applies each filter and sort correctly and skips empty values. */
class CarFilterServiceTest extends TestCase
{
    use RefreshDatabase;

    private CarFilterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CarFilterService();
    }

    public function test_no_filters_returns_all_cars_sorted_newest_first(): void
    {
        $older = Car::factory()->create(['created_at' => now()->subDay()]);
        $newer = Car::factory()->create(['created_at' => now()]);

        $result = $this->service->apply(Car::query(), [])->get();

        $this->assertCount(2, $result);
        $this->assertTrue($result->first()->is($newer));
        $this->assertTrue($result->last()->is($older));
    }

    public function test_filters_by_brand(): void
    {
        $brand = Brand::factory()->create();
        $match = Car::factory()->create(['brand_id' => $brand->id]);
        Car::factory()->create();

        $result = $this->service->apply(Car::query(), ['brand_id' => $brand->id])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_city_and_color_combined(): void
    {
        $city  = City::factory()->create();
        $color = Color::factory()->create();
        $match = Car::factory()->create(['city_id' => $city->id, 'color_id' => $color->id]);
        Car::factory()->create(['city_id' => $city->id]);
        Car::factory()->create();

        $result = $this->service->apply(Car::query(), [
            'city_id'  => $city->id,
            'color_id' => $color->id,
        ])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_price_range(): void
    {
        Car::factory()->create(['price' => 100000]);
        $match = Car::factory()->create(['price' => 500000]);
        Car::factory()->create(['price' => 900000]);

        $result = $this->service->apply(Car::query(), [
            'price_min' => 300000,
            'price_max' => 700000,
        ])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_year_range(): void
    {
        Car::factory()->create(['year' => 2012]);
        $match = Car::factory()->create(['year' => 2020]);

        $result = $this->service->apply(Car::query(), ['year_from' => 2018, 'year_to' => 2024])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_max_mileage(): void
    {
        $match = Car::factory()->create(['mileage' => 40000]);
        Car::factory()->create(['mileage' => 200000]);

        $result = $this->service->apply(Car::query(), ['mileage_max' => 100000])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_inspection_availability(): void
    {
        $match = Car::factory()->create(['has_inspection_report' => true]);
        Car::factory()->create(['has_inspection_report' => false]);

        $result = $this->service->apply(Car::query(), ['has_inspection' => true])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_search_matches_arabic_and_english_titles(): void
    {
        $arabicMatch  = Car::factory()->create(['title_ar' => 'تويوتا كامري مميزة', 'title_en' => 'nothing']);
        $englishMatch = Car::factory()->create(['title_ar' => 'أخرى', 'title_en' => 'Toyota Camry 2020']);
        Car::factory()->create(['title_ar' => 'هيونداي', 'title_en' => 'Hyundai']);

        $arabicResult  = $this->service->apply(Car::query(), ['search' => 'كامري'])->get();
        $englishResult = $this->service->apply(Car::query(), ['search' => 'Camry'])->get();

        $this->assertCount(1, $arabicResult);
        $this->assertTrue($arabicResult->first()->is($arabicMatch));
        $this->assertCount(1, $englishResult);
        $this->assertTrue($englishResult->first()->is($englishMatch));
    }

    public function test_sorts_by_price_ascending(): void
    {
        $expensive = Car::factory()->create(['price' => 900000]);
        $cheap     = Car::factory()->create(['price' => 100000]);

        $result = $this->service->apply(Car::query(), ['sort_by' => 'price_asc'])->get();

        $this->assertTrue($result->first()->is($cheap));
        $this->assertTrue($result->last()->is($expensive));
    }

    public function test_sorts_by_price_descending(): void
    {
        $cheap     = Car::factory()->create(['price' => 100000]);
        $expensive = Car::factory()->create(['price' => 900000]);

        $result = $this->service->apply(Car::query(), ['sort_by' => 'price_desc'])->get();

        $this->assertTrue($result->first()->is($expensive));
    }

    public function test_invalid_sort_value_falls_back_to_newest(): void
    {
        $older = Car::factory()->create(['created_at' => now()->subDay()]);
        $newer = Car::factory()->create(['created_at' => now()]);

        $result = $this->service->apply(Car::query(), ['sort_by' => 'bogus'])->get();

        $this->assertTrue($result->first()->is($newer));
    }

    public function test_empty_filter_values_are_ignored(): void
    {
        Car::factory()->count(2)->create();

        $result = $this->service->apply(Car::query(), [
            'brand_id' => null,
            'search'   => '',
        ])->get();

        $this->assertCount(2, $result);
    }

    public function test_filters_by_car_model_id(): void
    {
        $carModel = CarModel::factory()->create();
        $match = Car::factory()->create(['car_model_id' => $carModel->id]);
        Car::factory()->create();

        $result = $this->service->apply(Car::query(), ['car_model_id' => $carModel->id])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_condition(): void
    {
        $match = Car::factory()->create(['condition' => 'new']);
        Car::factory()->create(['condition' => 'used']);

        $result = $this->service->apply(Car::query(), ['condition' => 'new'])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_transmission(): void
    {
        $match = Car::factory()->create(['transmission' => 'manual']);
        Car::factory()->create(['transmission' => 'automatic']);

        $result = $this->service->apply(Car::query(), ['transmission' => 'manual'])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_fuel_type(): void
    {
        $match = Car::factory()->create(['fuel_type' => 'diesel']);
        Car::factory()->create(['fuel_type' => 'petrol']);

        $result = $this->service->apply(Car::query(), ['fuel_type' => 'diesel'])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_body_type(): void
    {
        $match = Car::factory()->create(['body_type' => 'suv']);
        Car::factory()->create(['body_type' => 'sedan']);

        $result = $this->service->apply(Car::query(), ['body_type' => 'suv'])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_payment_type(): void
    {
        $match = Car::factory()->create(['payment_type' => 'installment']);
        Car::factory()->create(['payment_type' => 'cash']);

        $result = $this->service->apply(Car::query(), ['payment_type' => 'installment'])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_filters_by_seller_type(): void
    {
        $match = Car::factory()->create(['seller_type' => 'individual']);
        Car::factory()->create(['seller_type' => 'admin']);

        $result = $this->service->apply(Car::query(), ['seller_type' => 'individual'])->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($match));
    }

    public function test_sorts_by_oldest(): void
    {
        $older = Car::factory()->create(['created_at' => now()->subDay()]);
        $newer = Car::factory()->create(['created_at' => now()]);

        $result = $this->service->apply(Car::query(), ['sort_by' => 'oldest'])->get();

        $this->assertTrue($result->first()->is($older));
        $this->assertTrue($result->last()->is($newer));
    }

    public function test_sorts_by_mileage_ascending(): void
    {
        $highMileage = Car::factory()->create(['mileage' => 200000]);
        $lowMileage = Car::factory()->create(['mileage' => 10000]);

        $result = $this->service->apply(Car::query(), ['sort_by' => 'mileage_asc'])->get();

        $this->assertTrue($result->first()->is($lowMileage));
        $this->assertTrue($result->last()->is($highMileage));
    }
}

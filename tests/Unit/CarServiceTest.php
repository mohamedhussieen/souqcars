<?php

namespace Tests\Unit;

use App\Enums\CarStatus;
use App\Exceptions\CarImageLimitExceededException;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\City;
use App\Services\CarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Verifies CarService creation, image-limit enforcement, status transitions, and stat counters. */
class CarServiceTest extends TestCase
{
    use RefreshDatabase;

    private CarService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CarService();
    }

    private function carData(): array
    {
        return [
            'seller_type'  => 'admin',
            'seller_id'    => null,
            'brand_id'     => Brand::factory()->create()->id,
            'car_model_id' => CarModel::factory()->create()->id,
            'city_id'      => City::factory()->create()->id,
            'year'         => 2022,
            'title_ar'     => 'سيارة تجريبية',
            'price'        => 250000,
            'transmission' => 'automatic',
            'fuel_type'    => 'petrol',
            'body_type'    => 'sedan',
        ];
    }

    public function test_create_persists_a_car(): void
    {
        $car = $this->service->create($this->carData());

        $this->assertDatabaseHas('cars', ['id' => $car->id, 'title_ar' => 'سيارة تجريبية']);
    }

    public function test_upload_images_accepts_up_to_ten_files(): void
    {
        $car = Car::factory()->create();
        $files = array_map(
            fn ($i) => UploadedFile::fake()->image("car{$i}.jpg"),
            range(1, 10)
        );

        $this->service->uploadImages($car, $files);

        $this->assertSame(10, $car->getMedia('car_images')->count());
    }

    public function test_upload_images_throws_when_exceeding_ten_total(): void
    {
        $car = Car::factory()->create();
        $this->service->uploadImages($car, array_map(
            fn ($i) => UploadedFile::fake()->image("car{$i}.jpg"),
            range(1, 8)
        ));

        $this->expectException(CarImageLimitExceededException::class);

        $this->service->uploadImages($car, array_map(
            fn ($i) => UploadedFile::fake()->image("more{$i}.jpg"),
            range(1, 5)
        ));
    }

    public function test_upload_images_does_not_add_any_when_the_batch_would_exceed_the_limit(): void
    {
        $car = Car::factory()->create();
        $this->service->uploadImages($car, [UploadedFile::fake()->image('a.jpg')]);

        try {
            $this->service->uploadImages($car, array_map(
                fn ($i) => UploadedFile::fake()->image("b{$i}.jpg"),
                range(1, 10)
            ));
        } catch (CarImageLimitExceededException) {
            // expected
        }

        $this->assertSame(1, $car->getMedia('car_images')->count());
    }

    public function test_mark_as_sold_updates_status(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Active]);

        $this->service->markAsSold($car);

        $this->assertSame(CarStatus::Sold, $car->fresh()->status);
    }

    public function test_increment_views_increases_counter(): void
    {
        $car = Car::factory()->create(['views_count' => 5]);

        $this->service->incrementViews($car);

        $this->assertSame(6, $car->fresh()->views_count);
    }

    public function test_delete_soft_deletes_the_car(): void
    {
        $car = Car::factory()->create();

        $this->service->delete($car);

        $this->assertSoftDeleted('cars', ['id' => $car->id]);
    }
}

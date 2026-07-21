<?php

namespace Tests\Feature\Admin;

use App\Enums\CarStatus;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\City;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Verifies the admin car listing CRUD, status workflow, and media upload endpoints. */
class AdminCarsTest extends TestCase
{
    use RefreshDatabase;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $this->adminToken = $admin->createToken('admin-dashboard')->plainTextToken;
    }

    private function asAdmin()
    {
        return $this->withHeader('Authorization', "Bearer {$this->adminToken}");
    }

    private function carPayload(array $overrides = []): array
    {
        return array_merge([
            'brand_id'     => Brand::factory()->create()->id,
            'car_model_id' => CarModel::factory()->create()->id,
            'city_id'      => City::factory()->create()->id,
            'year'         => 2022,
            'title_ar'     => 'سيارة تجريبية',
            'price'        => 250000,
            'transmission' => 'automatic',
            'fuel_type'    => 'petrol',
            'body_type'    => 'sedan',
        ], $overrides);
    }

    public function test_list_cars_returns_cars_of_any_status(): void
    {
        Car::factory()->create(['status' => CarStatus::Pending]);
        Car::factory()->create(['status' => CarStatus::Active]);

        $response = $this->asAdmin()->getJson('/api/admin/cars');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_list_cars_filters_by_status(): void
    {
        Car::factory()->create(['status' => CarStatus::Pending]);
        Car::factory()->create(['status' => CarStatus::Active]);

        $response = $this->asAdmin()->getJson('/api/admin/cars?status=pending');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_store_car_creates_a_car_as_admin_seller(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/cars', $this->carPayload());

        $response->assertStatus(201);
        $this->assertDatabaseHas('cars', ['seller_type' => 'admin', 'seller_id' => null]);
    }

    public function test_store_car_validation_requires_core_fields(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/cars', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['brand_id', 'car_model_id', 'city_id', 'year', 'title_ar', 'price']);
    }

    public function test_show_car_returns_a_car_regardless_of_status(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Pending]);

        $response = $this->asAdmin()->getJson("/api/admin/cars/{$car->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $car->id);
    }

    public function test_update_car_changes_fields(): void
    {
        $car = Car::factory()->create(['price' => 100000]);

        $response = $this->asAdmin()->putJson("/api/admin/cars/{$car->id}", ['price' => 200000]);

        $response->assertStatus(200)->assertJsonPath('data.price', 200000);
    }

    public function test_delete_car_soft_deletes_it(): void
    {
        $car = Car::factory()->create();

        $response = $this->asAdmin()->deleteJson("/api/admin/cars/{$car->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('cars', ['id' => $car->id]);
    }

    public function test_update_car_status_to_rejected_requires_a_reason(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Pending]);

        $response = $this->asAdmin()->putJson("/api/admin/cars/{$car->id}/status", ['status' => 'rejected']);

        $response->assertStatus(422)->assertJsonValidationErrors(['rejection_reason']);
    }

    public function test_update_car_status_approves_the_car(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Pending]);

        $response = $this->asAdmin()->putJson("/api/admin/cars/{$car->id}/status", ['status' => 'active']);

        $response->assertStatus(200)->assertJsonPath('data.status', 'active');
    }

    public function test_mark_sold_transitions_the_car(): void
    {
        $car = Car::factory()->create(['status' => CarStatus::Active]);

        $response = $this->asAdmin()->postJson("/api/admin/cars/{$car->id}/sold");

        $response->assertStatus(200)->assertJsonPath('data.status', 'sold');
    }

    public function test_upload_car_images_stores_the_files(): void
    {
        $car = Car::factory()->create();

        $response = $this->asAdmin()->post("/api/admin/cars/{$car->id}/images", [
            'images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
        ]);

        $response->assertStatus(200)->assertJsonPath('data.uploaded_count', 2);
    }

    public function test_delete_car_image_removes_it(): void
    {
        $car = Car::factory()->create();
        $media = $car->addMedia(UploadedFile::fake()->image('a.jpg'))->toMediaCollection('car_images');

        $response = $this->asAdmin()->deleteJson("/api/admin/cars/{$car->id}/images/{$media->id}");

        $response->assertStatus(200);
        $this->assertSame(0, $car->fresh()->getMedia('car_images')->count());
    }

    public function test_upload_inspection_stores_the_report(): void
    {
        $car = Car::factory()->create();

        $response = $this->asAdmin()->post("/api/admin/cars/{$car->id}/inspection", [
            'file' => UploadedFile::fake()->image('report.jpg'),
        ]);

        $response->assertStatus(200)->assertJsonStructure(['data' => ['inspection_url']]);
    }

    public function test_cars_endpoints_require_admin_role(): void
    {
        $response = $this->getJson('/api/admin/cars');

        $response->assertStatus(401);
    }
}

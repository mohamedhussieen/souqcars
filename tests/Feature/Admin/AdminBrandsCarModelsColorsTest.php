<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Color;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Verifies the admin brands, car models, and colors CRUD endpoints. */
class AdminBrandsCarModelsColorsTest extends TestCase
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

    // Brands

    public function test_list_brands_returns_paginated_brands(): void
    {
        Brand::factory()->count(2)->create();

        $response = $this->asAdmin()->getJson('/api/admin/brands');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_store_brand_creates_a_new_brand(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/brands', ['name_ar' => 'تويوتا', 'name_en' => 'Toyota']);

        $response->assertStatus(201)->assertJsonPath('data.name_en', 'Toyota');
    }

    public function test_store_brand_accepts_a_logo_upload(): void
    {
        $response = $this->asAdmin()->post('/api/admin/brands', [
            'name_ar' => 'تويوتا',
            'name_en' => 'Toyota',
            'logo'    => UploadedFile::fake()->image('logo.jpg'),
        ]);

        $response->assertStatus(201);
    }

    public function test_update_brand_changes_the_names(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->asAdmin()->putJson("/api/admin/brands/{$brand->id}", ['name_ar' => 'جديد', 'name_en' => 'New']);

        $response->assertStatus(200)->assertJsonPath('data.name_en', 'New');
    }

    public function test_delete_brand_removes_it(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->asAdmin()->deleteJson("/api/admin/brands/{$brand->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    // Car Models

    public function test_list_car_models_filters_by_brand(): void
    {
        $brand = Brand::factory()->create();
        CarModel::factory()->create(['brand_id' => $brand->id]);
        CarModel::factory()->create();

        $response = $this->asAdmin()->getJson("/api/admin/car-models?brand_id={$brand->id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_store_car_model_creates_a_new_model(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->asAdmin()->postJson('/api/admin/car-models', [
            'brand_id' => $brand->id,
            'name_ar'  => 'كامري',
            'name_en'  => 'Camry',
        ]);

        $response->assertStatus(201)->assertJsonPath('data.name_en', 'Camry');
    }

    public function test_store_car_model_validation_requires_existing_brand(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/car-models', [
            'brand_id' => 999999,
            'name_ar'  => 'كامري',
            'name_en'  => 'Camry',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['brand_id']);
    }

    public function test_update_car_model_changes_fields(): void
    {
        $carModel = CarModel::factory()->create();

        $response = $this->asAdmin()->putJson("/api/admin/car-models/{$carModel->id}", [
            'brand_id' => $carModel->brand_id,
            'name_ar'  => 'جديد',
            'name_en'  => 'New',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.name_en', 'New');
    }

    public function test_delete_car_model_removes_it(): void
    {
        $carModel = CarModel::factory()->create();

        $response = $this->asAdmin()->deleteJson("/api/admin/car-models/{$carModel->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('car_models', ['id' => $carModel->id]);
    }

    // Colors

    public function test_list_colors_returns_paginated_colors(): void
    {
        Color::factory()->count(2)->create();

        $response = $this->asAdmin()->getJson('/api/admin/colors');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_store_color_creates_a_new_color(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/colors', ['name_ar' => 'أحمر', 'name_en' => 'Red']);

        $response->assertStatus(201)->assertJsonPath('data.name_en', 'Red');
    }

    public function test_update_color_can_toggle_is_active(): void
    {
        $color = Color::factory()->create(['is_active' => true]);

        $response = $this->asAdmin()->putJson("/api/admin/colors/{$color->id}", ['is_active' => false]);

        $response->assertStatus(200);
        $this->assertFalse($color->fresh()->is_active);
    }

    public function test_delete_color_removes_unused_color(): void
    {
        $color = Color::factory()->create();

        $response = $this->asAdmin()->deleteJson("/api/admin/colors/{$color->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('colors', ['id' => $color->id]);
    }

    public function test_delete_color_refuses_when_a_car_uses_it(): void
    {
        $color = Color::factory()->create();
        Car::factory()->create(['color_id' => $color->id]);

        $response = $this->asAdmin()->deleteJson("/api/admin/colors/{$color->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('colors', ['id' => $color->id]);
    }
}

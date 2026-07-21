<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\CarModel;
use App\Services\AdminCarModelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies AdminCarModelService listing (optionally brand-filtered), creation, update, and deletion. */
class AdminCarModelServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminCarModelService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdminCarModelService();
    }

    public function test_list_returns_all_models_when_unfiltered(): void
    {
        CarModel::factory()->count(3)->create();

        $result = $this->service->list(10, null);

        $this->assertSame(3, $result->total());
    }

    public function test_list_filters_by_brand_id(): void
    {
        $brand = Brand::factory()->create();
        CarModel::factory()->count(2)->create(['brand_id' => $brand->id]);
        CarModel::factory()->create();

        $result = $this->service->list(10, $brand->id);

        $this->assertSame(2, $result->total());
    }

    public function test_create_persists_a_new_car_model(): void
    {
        $brand = Brand::factory()->create();

        $model = $this->service->create(['brand_id' => $brand->id, 'name_ar' => 'كامري', 'name_en' => 'Camry']);

        $this->assertDatabaseHas('car_models', ['name_en' => 'Camry', 'brand_id' => $brand->id]);
        $this->assertSame('Camry', $model->name_en);
    }

    public function test_update_changes_car_model_fields(): void
    {
        $carModel = CarModel::factory()->create(['name_en' => 'Old']);
        $newBrand = Brand::factory()->create();

        $updated = $this->service->update($carModel, ['brand_id' => $newBrand->id, 'name_ar' => $carModel->name_ar, 'name_en' => 'New']);

        $this->assertSame('New', $updated->name_en);
        $this->assertSame($newBrand->id, $updated->brand_id);
    }

    public function test_delete_removes_the_car_model(): void
    {
        $carModel = CarModel::factory()->create();

        $this->service->delete($carModel);

        $this->assertDatabaseMissing('car_models', ['id' => $carModel->id]);
    }
}

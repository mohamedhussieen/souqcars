<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Services\AdminBrandService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Verifies AdminBrandService listing, creation, update, and deletion of brands. */
class AdminBrandServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminBrandService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdminBrandService();
    }

    public function test_list_returns_paginated_brands(): void
    {
        Brand::factory()->count(3)->create();

        $result = $this->service->list(10);

        $this->assertSame(3, $result->total());
    }

    public function test_create_persists_a_new_brand(): void
    {
        $brand = $this->service->create(['name_ar' => 'تويوتا', 'name_en' => 'Toyota']);

        $this->assertDatabaseHas('brands', ['name_en' => 'Toyota']);
        $this->assertSame('Toyota', $brand->name_en);
    }

    public function test_create_attaches_logo_when_provided(): void
    {
        $logo = UploadedFile::fake()->image('logo.jpg');

        $brand = $this->service->create(['name_ar' => 'تويوتا', 'name_en' => 'Toyota'], $logo);

        $this->assertSame(1, $brand->getMedia('logo')->count());
    }

    public function test_update_changes_brand_names(): void
    {
        $brand = Brand::factory()->create(['name_en' => 'Old']);

        $updated = $this->service->update($brand, ['name_ar' => $brand->name_ar, 'name_en' => 'New']);

        $this->assertSame('New', $updated->name_en);
    }

    public function test_delete_removes_the_brand(): void
    {
        $brand = Brand::factory()->create();

        $this->service->delete($brand);

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }
}

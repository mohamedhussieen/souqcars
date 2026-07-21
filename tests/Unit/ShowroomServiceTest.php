<?php

namespace Tests\Unit;

use App\Models\Showroom;
use App\Services\ShowroomService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Verifies ShowroomService lazily creates and updates the single showroom profile row. */
class ShowroomServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShowroomService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ShowroomService();
    }

    public function test_get_creates_a_default_showroom_when_none_exists(): void
    {
        $showroom = $this->service->get();

        $this->assertDatabaseCount('showrooms', 1);
        $this->assertSame('Main Showroom', $showroom->name_en);
    }

    public function test_get_returns_the_existing_showroom_without_creating_another(): void
    {
        $existing = Showroom::factory()->create();

        $showroom = $this->service->get();

        $this->assertTrue($showroom->is($existing));
        $this->assertDatabaseCount('showrooms', 1);
    }

    public function test_update_changes_showroom_fields(): void
    {
        $showroom = Showroom::factory()->create(['name_en' => 'Old Name']);

        $updated = $this->service->update($showroom, ['name_en' => 'New Name', 'name_ar' => $showroom->name_ar, 'phone' => $showroom->phone]);

        $this->assertSame('New Name', $updated->name_en);
    }

    public function test_update_attaches_logo_when_provided(): void
    {
        $showroom = Showroom::factory()->create();
        $logo = UploadedFile::fake()->image('logo.jpg');

        $this->service->update($showroom, [], $logo);

        $this->assertSame(1, $showroom->fresh()->getMedia('logo')->count());
    }
}

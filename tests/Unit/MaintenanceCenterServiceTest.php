<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\User;
use App\Services\MaintenanceCenterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies MaintenanceCenterService CRUD and the delete-blocked-by-bookings rule. */
class MaintenanceCenterServiceTest extends TestCase
{
    use RefreshDatabase;

    private MaintenanceCenterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MaintenanceCenterService();
    }

    public function test_list_returns_only_active_centers(): void
    {
        MaintenanceCenter::factory()->create(['is_active' => true]);
        MaintenanceCenter::factory()->create(['is_active' => false]);

        $paginator = $this->service->list(15);

        $this->assertSame(1, $paginator->total());
    }

    public function test_services_returns_active_services_ordered_by_sort_order(): void
    {
        $center = MaintenanceCenter::factory()->create();
        MaintenanceService::factory()->create(['maintenance_center_id' => $center->id, 'sort_order' => 2, 'is_active' => true]);
        MaintenanceService::factory()->create(['maintenance_center_id' => $center->id, 'sort_order' => 1, 'is_active' => true]);
        MaintenanceService::factory()->create(['maintenance_center_id' => $center->id, 'is_active' => false]);

        $services = $this->service->services($center);

        $this->assertCount(2, $services);
        $this->assertSame(1, $services->first()->sort_order);
    }

    public function test_delete_throws_when_center_has_pending_or_confirmed_bookings(): void
    {
        $center = MaintenanceCenter::factory()->create();
        $service = MaintenanceService::factory()->create(['maintenance_center_id' => $center->id]);
        Booking::create([
            'user_id' => User::factory()->create()->id,
            'maintenance_center_id' => $center->id,
            'maintenance_service_id' => $service->id,
            'status' => 'pending',
            'date' => now()->addDay()->toDateString(),
            'time' => '10:00:00',
            'price' => $service->price,
        ]);

        $this->expectException(\App\Exceptions\HasDependentRecordsException::class);

        $this->service->delete($center);
    }

    public function test_delete_succeeds_when_no_active_bookings(): void
    {
        $center = MaintenanceCenter::factory()->create();

        $this->service->delete($center);

        $this->assertSoftDeleted($center);
    }
}

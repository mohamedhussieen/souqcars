<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\User;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Verifies BookingService creation, conflict detection, cancellation, and admin status transitions. */
class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingService(new NotificationService());
    }

    private function makeCenterAndService(): array
    {
        $center = MaintenanceCenter::factory()->create();
        $service = MaintenanceService::factory()->create(['maintenance_center_id' => $center->id, 'price' => 100]);

        return [$center, $service];
    }

    public function test_create_snapshots_service_price_and_notifies_user(): void
    {
        [$center, $service] = $this->makeCenterAndService();
        $user = User::factory()->create();

        $booking = $this->service->create($user, [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => now()->addDay()->toDateString(),
            'time'                   => '10:00:00',
        ]);

        $this->assertEquals(100, (float) $booking->price);
        $this->assertSame(BookingStatus::Pending, $booking->status);
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'booking_confirmed']);
    }

    public function test_create_throws_conflict_for_same_user_center_and_date(): void
    {
        [$center, $service] = $this->makeCenterAndService();
        $user = User::factory()->create();
        $date = now()->addDay()->toDateString();

        $this->service->create($user, [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => $date,
            'time'                   => '10:00:00',
        ]);

        $this->expectException(\App\Exceptions\BookingConflictException::class);

        $this->service->create($user, [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => $date,
            'time'                   => '14:00:00',
        ]);
    }

    public function test_owner_can_cancel_own_booking(): void
    {
        [$center, $service] = $this->makeCenterAndService();
        $user = User::factory()->create();
        $booking = $this->service->create($user, [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => now()->addDay()->toDateString(),
            'time'                   => '10:00:00',
        ]);

        $cancelled = $this->service->cancel($booking, 'change of plans', $user);

        $this->assertSame(BookingStatus::Cancelled, $cancelled->status);
    }

    public function test_non_owner_non_admin_cannot_cancel_booking(): void
    {
        [$center, $service] = $this->makeCenterAndService();
        $user = User::factory()->create();
        $stranger = User::factory()->create();
        $booking = $this->service->create($user, [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => now()->addDay()->toDateString(),
            'time'                   => '10:00:00',
        ]);

        $this->expectException(\App\Exceptions\NotOwnerException::class);

        $this->service->cancel($booking, null, $stranger);
    }

    public function test_admin_can_update_status_through_valid_transitions(): void
    {
        Role::findOrCreate(UserRole::Admin->value, 'sanctum');
        [$center, $service] = $this->makeCenterAndService();
        $user = User::factory()->create();
        $booking = $this->service->create($user, [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => now()->addDay()->toDateString(),
            'time'                   => '10:00:00',
        ]);

        $confirmed = $this->service->updateStatus($booking, BookingStatus::Confirmed);
        $this->assertSame(BookingStatus::Confirmed, $confirmed->status);

        $completed = $this->service->updateStatus($confirmed, BookingStatus::Completed);
        $this->assertSame(BookingStatus::Completed, $completed->status);
    }

    public function test_invalid_status_transition_throws(): void
    {
        [$center, $service] = $this->makeCenterAndService();
        $user = User::factory()->create();
        $booking = $this->service->create($user, [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => now()->addDay()->toDateString(),
            'time'                   => '10:00:00',
        ]);

        $this->expectException(\App\Exceptions\InvalidBookingStatusTransitionException::class);

        $this->service->updateStatus($booking, BookingStatus::Completed);
    }
}

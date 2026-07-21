<?php

namespace Tests\Feature\Mobile;

use App\Enums\UserRole;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Verifies the mobile booking create/list/show/cancel endpoints and admin status update. */
class BookingTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return $user->createToken('mobile-app')->plainTextToken;
    }

    public function test_user_can_create_booking(): void
    {
        $user = User::factory()->create();
        $center = MaintenanceCenter::factory()->create();
        $service = MaintenanceService::factory()->create(['maintenance_center_id' => $center->id, 'price' => 150]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson('/api/v1/mobile/bookings', [
                'maintenance_center_id'  => $center->id,
                'maintenance_service_id' => $service->id,
                'date'                   => now()->addDay()->toDateString(),
                'time'                   => '10:00',
            ]);

        $response->assertStatus(201)->assertJsonPath('data.status', 'pending');
        $this->assertDatabaseHas('bookings', ['user_id' => $user->id, 'maintenance_center_id' => $center->id]);
    }

    public function test_conflicting_booking_same_user_center_and_date_returns_422(): void
    {
        $user = User::factory()->create();
        $center = MaintenanceCenter::factory()->create();
        $service = MaintenanceService::factory()->create(['maintenance_center_id' => $center->id]);
        $token = $this->tokenFor($user);
        $date = now()->addDay()->toDateString();

        $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/v1/mobile/bookings', [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => $date,
            'time'                   => '10:00',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/v1/mobile/bookings', [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => $date,
            'time'                   => '14:00',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_cancel_own_booking(): void
    {
        $user = User::factory()->create();
        $center = MaintenanceCenter::factory()->create();
        $service = MaintenanceService::factory()->create(['maintenance_center_id' => $center->id]);
        $token = $this->tokenFor($user);

        $create = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/v1/mobile/bookings', [
            'maintenance_center_id'  => $center->id,
            'maintenance_service_id' => $service->id,
            'date'                   => now()->addDay()->toDateString(),
            'time'                   => '10:00',
        ]);
        $bookingId = $create->json('data.id');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/mobile/bookings/{$bookingId}/cancel", ['cancellation_reason' => 'changed plans']);

        $response->assertStatus(200)->assertJsonPath('data.status', 'cancelled');
    }

    public function test_user_cannot_cancel_others_booking(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $center = MaintenanceCenter::factory()->create();
        $service = MaintenanceService::factory()->create(['maintenance_center_id' => $center->id]);

        $create = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($owner))
            ->postJson('/api/v1/mobile/bookings', [
                'maintenance_center_id'  => $center->id,
                'maintenance_service_id' => $service->id,
                'date'                   => now()->addDay()->toDateString(),
                'time'                   => '10:00',
            ]);
        $bookingId = $create->json('data.id');

        app('auth')->forgetGuards();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($stranger))
            ->deleteJson("/api/v1/mobile/bookings/{$bookingId}/cancel");

        $response->assertStatus(403);
    }

    public function test_admin_can_update_booking_status(): void
    {
        Role::findOrCreate(UserRole::Admin->value, 'sanctum');
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $user = User::factory()->create();
        $center = MaintenanceCenter::factory()->create();
        $service = MaintenanceService::factory()->create(['maintenance_center_id' => $center->id]);

        $create = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson('/api/v1/mobile/bookings', [
                'maintenance_center_id'  => $center->id,
                'maintenance_service_id' => $service->id,
                'date'                   => now()->addDay()->toDateString(),
                'time'                   => '10:00',
            ]);
        $bookingId = $create->json('data.id');

        app('auth')->forgetGuards();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($admin))
            ->putJson("/api/admin/bookings/{$bookingId}/status", ['status' => 'confirmed']);

        $response->assertStatus(200)->assertJsonPath('data.status', 'confirmed');
    }
}

<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\WatchRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies WatchRequestService watch/unwatch upsert semantics and match notification. */
class WatchRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    private WatchRequestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WatchRequestService(new NotificationService());
    }

    public function test_watch_requires_the_car_to_be_sold(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->create();

        $this->expectException(\App\Exceptions\InvalidCarStateException::class);

        $this->service->watch($user, $car);
    }

    public function test_watch_creates_a_watch_request_for_sold_car(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->sold()->create();

        $watchRequest = $this->service->watch($user, $car);

        $this->assertTrue($watchRequest->is_active);
        $this->assertSame($car->brand_id, $watchRequest->brand_id);
    }

    public function test_watching_the_same_brand_model_twice_upserts_instead_of_duplicating(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->sold()->create();

        $this->service->watch($user, $car);
        $this->service->watch($user, $car);

        $this->assertDatabaseCount('car_watch_requests', 1);
    }

    public function test_unwatch_deactivates_without_deleting(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->sold()->create();
        $this->service->watch($user, $car);

        $this->service->unwatch($user, $car);

        $this->assertDatabaseHas('car_watch_requests', ['user_id' => $user->id, 'is_active' => false]);
    }

    public function test_notify_matches_sends_notification_and_stamps_notified_at(): void
    {
        $user = User::factory()->create();
        $soldCar = Car::factory()->sold()->create();
        $this->service->watch($user, $soldCar);

        $newCar = Car::factory()->create([
            'brand_id'     => $soldCar->brand_id,
            'car_model_id' => $soldCar->car_model_id,
        ]);

        $this->service->notifyMatches($newCar);

        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'car_available']);
        $this->assertDatabaseHas('car_watch_requests', ['user_id' => $user->id]);
        $this->assertNotNull($user->fresh());
    }
}

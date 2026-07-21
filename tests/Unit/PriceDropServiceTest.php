<?php

namespace Tests\Unit;

use App\Models\Car;
use App\Models\Favorite;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PriceDropService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies PriceDropService only notifies favoriters when the price actually decreased on an active car. */
class PriceDropServiceTest extends TestCase
{
    use RefreshDatabase;

    private PriceDropService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PriceDropService(new NotificationService());
    }

    public function test_notifies_users_who_favorited_the_car_on_price_drop(): void
    {
        $car = Car::factory()->create(['price' => 90000]);
        $user = User::factory()->create();
        Favorite::create(['user_id' => $user->id, 'car_id' => $car->id]);

        $this->service->notifyIfDropped($car, 100000);

        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'price_drop']);
    }

    public function test_does_not_notify_when_price_increased(): void
    {
        $car = Car::factory()->create(['price' => 110000]);
        $user = User::factory()->create();
        Favorite::create(['user_id' => $user->id, 'car_id' => $car->id]);

        $this->service->notifyIfDropped($car, 100000);

        $this->assertDatabaseMissing('notifications', ['user_id' => $user->id, 'type' => 'price_drop']);
    }

    public function test_does_not_notify_when_car_is_not_active(): void
    {
        $car = Car::factory()->pending()->create(['price' => 90000]);
        $user = User::factory()->create();
        Favorite::create(['user_id' => $user->id, 'car_id' => $car->id]);

        $this->service->notifyIfDropped($car, 100000);

        $this->assertDatabaseMissing('notifications', ['user_id' => $user->id, 'type' => 'price_drop']);
    }
}

<?php

namespace Tests\Feature\Mobile;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the mobile watch/unwatch endpoints and the notify-on-match job dispatch. */
class WatchRequestTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return $user->createToken('mobile-app')->plainTextToken;
    }

    public function test_user_can_watch_a_sold_car(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->sold()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson("/api/v1/mobile/cars/{$car->id}/watch");

        $response->assertStatus(200)->assertJsonPath('data.is_active', true);
        $this->assertDatabaseHas('car_watch_requests', ['user_id' => $user->id, 'brand_id' => $car->brand_id]);
    }

    public function test_watching_a_non_sold_car_returns_422(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson("/api/v1/mobile/cars/{$car->id}/watch");

        $response->assertStatus(422);
    }

    public function test_watching_the_same_car_twice_succeeds_as_upsert(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->sold()->create();
        $token = $this->tokenFor($user);

        $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/v1/mobile/cars/{$car->id}/watch");
        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/v1/mobile/cars/{$car->id}/watch");

        $response->assertStatus(200);
        $this->assertDatabaseCount('car_watch_requests', 1);
    }

    public function test_notification_dispatched_when_matching_car_goes_active(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $user = User::factory()->create();
        $soldCar = Car::factory()->sold()->create();
        $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson("/api/v1/mobile/cars/{$soldCar->id}/watch");

        Car::factory()->create([
            'brand_id'     => $soldCar->brand_id,
            'car_model_id' => $soldCar->car_model_id,
            'status'       => 'active',
        ]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWatchNotificationsJob::class);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Car;
use App\Models\User;
use App\Services\FavoriteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies FavoriteService toggling behavior and favorites_count bookkeeping. */
class FavoriteServiceTest extends TestCase
{
    use RefreshDatabase;

    private FavoriteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FavoriteService();
    }

    public function test_toggle_adds_favorite_when_absent(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->create();

        $result = $this->service->toggle($user, $car);

        $this->assertTrue($result['added']);
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'car_id' => $car->id]);
        $this->assertSame(1, $car->fresh()->favorites_count);
    }

    public function test_toggle_removes_favorite_when_present(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->create();
        $this->service->toggle($user, $car);

        $result = $this->service->toggle($user, $car);

        $this->assertFalse($result['added']);
        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'car_id' => $car->id]);
        $this->assertSame(0, $car->fresh()->favorites_count);
    }

    public function test_toggle_is_scoped_per_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $car   = Car::factory()->create();

        $this->service->toggle($userA, $car);
        $result = $this->service->toggle($userB, $car);

        $this->assertTrue($result['added']);
        $this->assertSame(2, $car->fresh()->favorites_count);
    }

    public function test_list_returns_only_the_users_favorites(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $mine  = Car::factory()->create();
        $their = Car::factory()->create();
        $this->service->toggle($user, $mine);
        $this->service->toggle($other, $their);

        $paginator = $this->service->list($user);

        $this->assertSame(1, $paginator->total());
        $this->assertSame($mine->id, $paginator->items()[0]->car_id);
    }
}

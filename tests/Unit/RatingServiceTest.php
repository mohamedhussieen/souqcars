<?php

namespace Tests\Unit;

use App\Models\Car;
use App\Models\User;
use App\Services\CarService;
use App\Services\RatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies RatingService add/update semantics and rating_avg recalculation on the car. */
class RatingServiceTest extends TestCase
{
    use RefreshDatabase;

    private RatingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RatingService(new CarService());
    }

    public function test_add_creates_a_new_rating_and_updates_car_average(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->create();

        $this->service->addOrUpdate($user, $car, 4, 'Great car');

        $this->assertDatabaseHas('car_ratings', [
            'user_id' => $user->id,
            'car_id'  => $car->id,
            'rating'  => 4,
        ]);
        $this->assertEquals(4.0, (float) $car->fresh()->rating_avg);
    }

    public function test_update_replaces_the_same_users_existing_rating_instead_of_duplicating(): void
    {
        $user = User::factory()->create();
        $car  = Car::factory()->create();

        $this->service->addOrUpdate($user, $car, 3, 'Ok');
        $this->service->addOrUpdate($user, $car, 5, 'Actually great');

        $this->assertSame(1, $car->ratings()->count());
        $this->assertEquals(5.0, (float) $car->fresh()->rating_avg);
    }

    public function test_average_is_recalculated_across_multiple_users(): void
    {
        $car    = Car::factory()->create();
        $userA  = User::factory()->create();
        $userB  = User::factory()->create();

        $this->service->addOrUpdate($userA, $car, 2);
        $this->service->addOrUpdate($userB, $car, 4);

        $this->assertEquals(3.0, (float) $car->fresh()->rating_avg);
    }

    public function test_list_returns_ratings_most_recent_first(): void
    {
        $car   = Car::factory()->create();
        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->service->addOrUpdate($first, $car, 3);
        $this->service->addOrUpdate($second, $car, 5);

        $paginator = $this->service->list($car);

        $this->assertSame(2, $paginator->total());
        $this->assertSame($second->id, $paginator->items()[0]->user_id);
    }
}

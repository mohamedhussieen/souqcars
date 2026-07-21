<?php

namespace Tests\Feature\Mobile;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the mobile favorites toggle/list and car ratings endpoints. */
class FavoritesAndRatingsTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return $user->createToken('mobile-app')->plainTextToken;
    }

    public function test_toggle_favorite_adds_when_absent(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson("/api/v1/mobile/favorites/{$car->id}");

        $response->assertStatus(200)->assertJsonPath('data.added', true);
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'car_id' => $car->id]);
    }

    public function test_toggle_favorite_removes_when_present(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();
        $token = $this->tokenFor($user);
        $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/v1/mobile/favorites/{$car->id}");

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/v1/mobile/favorites/{$car->id}");

        $response->assertStatus(200)->assertJsonPath('data.added', false);
    }

    public function test_toggle_favorite_requires_authentication(): void
    {
        $car = Car::factory()->create();

        $response = $this->postJson("/api/v1/mobile/favorites/{$car->id}");

        $response->assertStatus(401);
    }

    public function test_list_favorites_returns_only_the_users_favorites(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();
        $token = $this->tokenFor($user);
        $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/v1/mobile/favorites/{$car->id}");

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/v1/mobile/favorites');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_store_car_rating_persists_the_rating(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson("/api/v1/mobile/cars/{$car->id}/ratings", ['rating' => 5, 'comment' => 'Great!']);

        $response->assertStatus(200)->assertJsonPath('data.rating', 5);
        $this->assertDatabaseHas('car_ratings', ['user_id' => $user->id, 'car_id' => $car->id, 'rating' => 5]);
    }

    public function test_store_car_rating_requires_authentication(): void
    {
        $car = Car::factory()->create();

        $response = $this->postJson("/api/v1/mobile/cars/{$car->id}/ratings", ['rating' => 5]);

        $response->assertStatus(401);
    }

    public function test_store_car_rating_validation_rejects_out_of_range_rating(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson("/api/v1/mobile/cars/{$car->id}/ratings", ['rating' => 6]);

        $response->assertStatus(422)->assertJsonValidationErrors(['rating']);
    }

    public function test_list_car_ratings_returns_paginated_ratings(): void
    {
        $car = Car::factory()->create();
        $user = User::factory()->create();
        $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson("/api/v1/mobile/cars/{$car->id}/ratings", ['rating' => 4]);

        $response = $this->getJson("/api/v1/mobile/cars/{$car->id}/ratings");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}

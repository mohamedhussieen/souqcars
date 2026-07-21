<?php

namespace Tests\Feature\Mobile;

use App\Enums\CarStatus;
use App\Enums\SellerType;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the mobile home aggregation endpoint and the authenticated my-listings endpoint. */
class HomeAndMyListingsTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return $user->createToken('mobile-app')->plainTextToken;
    }

    public function test_home_returns_all_expected_sections(): void
    {
        Car::factory()->create(['status' => CarStatus::Active]);

        $response = $this->getJson('/api/v1/mobile/home');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['ads', 'brands', 'customer_cars', 'latest_cars', 'showrooms', 'featured_cars']]);
    }

    public function test_home_works_for_guests(): void
    {
        $response = $this->getJson('/api/v1/mobile/home');

        $response->assertStatus(200)->assertJsonPath('success', true);
    }

    public function test_my_listings_returns_only_the_users_individual_listings(): void
    {
        $user = User::factory()->create();
        $mine = Car::factory()->create(['seller_type' => SellerType::Individual, 'seller_id' => $user->id]);
        Car::factory()->create(['seller_type' => SellerType::Individual, 'seller_id' => User::factory()->create()->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->getJson('/api/v1/mobile/my-listings');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($mine->id, $response->json('data.0.id'));
    }

    public function test_my_listings_includes_status_counts_in_meta(): void
    {
        $user = User::factory()->create();
        Car::factory()->create(['seller_type' => SellerType::Individual, 'seller_id' => $user->id, 'status' => CarStatus::Active]);
        Car::factory()->create(['seller_type' => SellerType::Individual, 'seller_id' => $user->id, 'status' => CarStatus::Sold]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->getJson('/api/v1/mobile/my-listings');

        $response->assertStatus(200)
            ->assertJsonPath('meta.active_count', 1)
            ->assertJsonPath('meta.sold_count', 1)
            ->assertJsonPath('meta.total', 2);
    }

    public function test_my_listings_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/mobile/my-listings');

        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the admin cities CRUD endpoints. */
class AdminCitiesTest extends TestCase
{
    use RefreshDatabase;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $this->adminToken = $admin->createToken('admin-dashboard')->plainTextToken;
    }

    private function asAdmin()
    {
        return $this->withHeader('Authorization', "Bearer {$this->adminToken}");
    }

    public function test_list_cities_returns_paginated_cities(): void
    {
        City::factory()->count(2)->create();

        $response = $this->asAdmin()->getJson('/api/admin/cities');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_store_city_creates_a_new_city(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/cities', ['name_ar' => 'القاهرة', 'name_en' => 'Cairo']);

        $response->assertStatus(201)->assertJsonPath('data.name_en', 'Cairo');
        $this->assertDatabaseHas('cities', ['name_en' => 'Cairo']);
    }

    public function test_store_city_validation_requires_both_names(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/cities', ['name_ar' => 'القاهرة']);

        $response->assertStatus(422)->assertJsonValidationErrors(['name_en']);
    }

    public function test_update_city_changes_the_names(): void
    {
        $city = City::factory()->create();

        $response = $this->asAdmin()->putJson("/api/admin/cities/{$city->id}", ['name_ar' => 'جديد', 'name_en' => 'New']);

        $response->assertStatus(200)->assertJsonPath('data.name_en', 'New');
    }

    public function test_delete_city_removes_it(): void
    {
        $city = City::factory()->create();

        $response = $this->asAdmin()->deleteJson("/api/admin/cities/{$city->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }

    public function test_cities_endpoints_require_admin_role(): void
    {
        $response = $this->getJson('/api/admin/cities');

        $response->assertStatus(401);
    }
}

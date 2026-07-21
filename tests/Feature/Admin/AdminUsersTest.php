<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the admin users management endpoints: list/search/filter, show, toggle-active, role update, delete. */
class AdminUsersTest extends TestCase
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

    public function test_list_users_returns_paginated_users(): void
    {
        User::factory()->count(2)->create();

        $response = $this->asAdmin()->getJson('/api/admin/users');

        $response->assertStatus(200)->assertJsonStructure(['data', 'meta']);
    }

    public function test_list_users_filters_by_search(): void
    {
        $match = User::factory()->create(['name' => 'Ahmed Hassan']);
        User::factory()->create(['name' => 'Someone Else']);

        $response = $this->asAdmin()->getJson('/api/admin/users?search=Ahmed');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_show_user_returns_a_single_user(): void
    {
        $user = User::factory()->create();

        $response = $this->asAdmin()->getJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200)->assertJsonPath('data.id', $user->id);
    }

    public function test_show_user_returns_404_for_missing_user(): void
    {
        $response = $this->asAdmin()->getJson('/api/admin/users/999999');

        $response->assertStatus(404);
    }

    public function test_toggle_active_flips_the_users_status(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->asAdmin()->putJson("/api/admin/users/{$user->id}/toggle-active");

        $response->assertStatus(200)->assertJsonPath('data.is_active', false);
    }

    public function test_update_role_changes_the_users_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::User->value);

        $response = $this->asAdmin()->putJson("/api/admin/users/{$user->id}/role", ['role' => UserRole::ShowroomOwner->value]);

        $response->assertStatus(200);
        $this->assertTrue($user->fresh()->hasRole(UserRole::ShowroomOwner->value));
    }

    public function test_update_role_validation_rejects_unknown_role(): void
    {
        $user = User::factory()->create();

        $response = $this->asAdmin()->putJson("/api/admin/users/{$user->id}/role", ['role' => 'superadmin']);

        $response->assertStatus(422);
    }

    public function test_delete_user_soft_deletes_the_account(): void
    {
        $user = User::factory()->create();

        $response = $this->asAdmin()->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}

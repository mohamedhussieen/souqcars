<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies admin dashboard login/me/logout and the admin-role middleware gate. */
class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_login_succeeds_for_admin(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'Password123']);
        $admin->assignRole(UserRole::Admin->value);

        $response = $this->postJson('/api/admin/auth/login', [
            'email'    => 'admin@example.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_login_fails_for_non_admin(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com', 'password' => 'Password123']);
        $user->assignRole(UserRole::User->value);

        $response = $this->postJson('/api/admin/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(401);
    }

    public function test_me_returns_the_authenticated_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $token = $admin->createToken('admin-dashboard')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/admin/auth/me');

        $response->assertStatus(200)->assertJsonPath('data.id', $admin->id);
    }

    public function test_logout_revokes_the_token(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $token = $admin->createToken('admin-dashboard')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/admin/auth/logout');

        $response->assertStatus(200);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_protected_admin_route_rejects_non_admin_token(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::User->value);
        $token = $user->createToken('mobile-app')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/admin/users');

        $response->assertStatus(401)->assertJsonPath('success', false);
    }

    public function test_protected_admin_route_rejects_unauthenticated_request(): void
    {
        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(401);
    }

    public function test_protected_admin_route_allows_admin_token(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $token = $admin->createToken('admin-dashboard')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/admin/users');

        $response->assertStatus(200);
    }
}

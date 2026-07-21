<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\AdminAuthService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies AdminAuthService restricts login to admin-role users and handles logout. */
class AdminAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminAuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->service = new AdminAuthService();
    }

    public function test_login_succeeds_for_admin_with_correct_credentials(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'Password123']);
        $admin->assignRole(UserRole::Admin->value);

        $result = $this->service->login('admin@example.com', 'Password123');

        $this->assertNotNull($result);
        $this->assertTrue($result['user']->is($admin));
        $this->assertNotEmpty($result['token']);
    }

    public function test_login_fails_for_non_admin_user_even_with_correct_password(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com', 'password' => 'Password123']);
        $user->assignRole(UserRole::User->value);

        $result = $this->service->login('user@example.com', 'Password123');

        $this->assertNull($result);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'Password123']);
        $admin->assignRole(UserRole::Admin->value);

        $result = $this->service->login('admin@example.com', 'WrongPassword');

        $this->assertNull($result);
    }

    public function test_login_fails_for_unknown_email(): void
    {
        $result = $this->service->login('nobody@example.com', 'Password123');

        $this->assertNull($result);
    }

    public function test_login_returns_inactive_flag_for_deactivated_admin(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'Password123', 'is_active' => false]);
        $admin->assignRole(UserRole::Admin->value);

        $result = $this->service->login('admin@example.com', 'Password123');

        $this->assertSame(['inactive' => true], $result);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $token = $admin->createToken('admin-dashboard');
        $admin->withAccessToken($token->accessToken);

        $this->service->logout($admin);

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->accessToken->id]);
    }
}

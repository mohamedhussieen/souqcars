<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\AdminUserService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies AdminUserService listing/search/filtering, activation toggling, role updates, and deletion. */
class AdminUserServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminUserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->service = new AdminUserService();
    }

    public function test_list_returns_all_users_when_unfiltered(): void
    {
        User::factory()->count(3)->create();

        $result = $this->service->list(10, null, null);

        $this->assertSame(3, $result->total());
    }

    public function test_list_filters_by_search_across_name_email_phone(): void
    {
        $match = User::factory()->create(['name' => 'Ahmed Hassan']);
        User::factory()->create(['name' => 'Someone Else']);

        $result = $this->service->list(10, 'Ahmed', null);

        $this->assertSame(1, $result->total());
        $this->assertSame($match->id, $result->items()[0]->id);
    }

    public function test_list_filters_by_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $regular = User::factory()->create();
        $regular->assignRole(UserRole::User->value);

        $result = $this->service->list(10, null, UserRole::Admin->value);

        $this->assertSame(1, $result->total());
        $this->assertSame($admin->id, $result->items()[0]->id);
    }

    public function test_toggle_active_flips_the_flag(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $updated = $this->service->toggleActive($user);

        $this->assertFalse($updated->is_active);
    }

    public function test_toggle_active_revokes_tokens_when_deactivating(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->createToken('mobile-app');

        $this->service->toggleActive($user);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_toggle_active_keeps_tokens_when_reactivating(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $user->createToken('mobile-app');

        $updated = $this->service->toggleActive($user);

        $this->assertTrue($updated->is_active);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_update_role_replaces_existing_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::User->value);

        $updated = $this->service->updateRole($user, UserRole::ShowroomOwner);

        $this->assertTrue($updated->hasRole(UserRole::ShowroomOwner->value));
        $this->assertFalse($updated->hasRole(UserRole::User->value));
    }

    public function test_delete_soft_deletes_and_revokes_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('mobile-app');

        $this->service->delete($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}

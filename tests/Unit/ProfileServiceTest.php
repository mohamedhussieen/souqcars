<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Verifies ProfileService updates, password change, preferences, deletion, and policy acceptance. */
class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProfileService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProfileService();
    }

    public function test_update_changes_name_and_phone(): void
    {
        $user = User::factory()->create(['name' => 'Old Name', 'phone' => '01000000000']);

        $updated = $this->service->update($user, ['name' => 'New Name', 'phone' => '01011111111']);

        $this->assertSame('New Name', $updated->name);
        $this->assertSame('01011111111', $updated->phone);
    }

    public function test_update_attaches_avatar_when_provided(): void
    {
        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $this->service->update($user, ['name' => $user->name, 'phone' => $user->phone], $avatar);

        $this->assertSame(1, $user->fresh()->getMedia('avatar')->count());
    }

    public function test_change_password_succeeds_with_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword123']);

        $result = $this->service->changePassword($user, 'OldPassword123', 'NewPassword123');

        $this->assertTrue($result);
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword123']);

        $result = $this->service->changePassword($user, 'WrongPassword', 'NewPassword123');

        $this->assertFalse($result);
        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_update_preferences_sets_notification_and_theme(): void
    {
        $user = User::factory()->create();

        $updated = $this->service->updatePreferences($user, false, 'dark');

        $this->assertFalse($updated->notification_enabled);
        $this->assertSame('dark', $updated->theme->value);
    }

    public function test_delete_account_soft_deletes_and_revokes_tokens(): void
    {
        $user = User::factory()->create(['email' => 'a@b.com', 'phone' => '01000000000']);
        $user->createToken('mobile-app');

        $this->service->deleteAccount($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_delete_account_frees_up_email_and_phone_for_reuse(): void
    {
        $user = User::factory()->create(['email' => 'a@b.com', 'phone' => '01000000000']);

        $this->service->deleteAccount($user);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'email' => "deleted_{$user->id}_a@b.com",
            'phone' => "deleted_{$user->id}_01000000000",
        ]);
    }

    public function test_accept_policy_sets_policy_accepted_at(): void
    {
        $user = User::factory()->create(['policy_accepted_at' => null]);

        $updated = $this->service->acceptPolicy($user);

        $this->assertNotNull($updated->policy_accepted_at);
        $this->assertTrue($updated->hasAcceptedPolicy());
    }
}

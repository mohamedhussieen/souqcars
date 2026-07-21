<?php

namespace Tests\Feature\Mobile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Verifies the mobile profile endpoints and the policy-acceptance gate on protected ones. */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(bool $policyAccepted = true): User
    {
        return User::factory()->create([
            'policy_accepted_at' => $policyAccepted ? now() : null,
        ]);
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('mobile-app')->plainTextToken;
    }

    public function test_show_profile_returns_the_authenticated_user(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->getJson('/api/v1/mobile/profile');

        $response->assertStatus(200)->assertJsonPath('data.id', $user->id);
    }

    public function test_show_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/mobile/profile');

        $response->assertStatus(401);
    }

    public function test_update_profile_succeeds_when_policy_accepted(): void
    {
        $user = $this->actingUser(policyAccepted: true);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson('/api/v1/mobile/profile', ['name' => 'New Name', 'phone' => '01099999999']);

        $response->assertStatus(200)->assertJsonPath('data.name', 'New Name');
    }

    public function test_update_profile_blocked_when_policy_not_accepted(): void
    {
        $user = $this->actingUser(policyAccepted: false);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson('/api/v1/mobile/profile', ['name' => 'New Name', 'phone' => '01099999999']);

        $response->assertStatus(403)->assertJsonPath('success', false);
    }

    public function test_update_profile_accepts_a_jpeg_avatar_and_sets_avatar_url(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->post('/api/v1/mobile/profile', [
                'name'    => $user->name,
                'phone'   => $user->phone,
                'avatar'  => UploadedFile::fake()->image('avatar.jpg'),
                '_method' => 'PUT',
            ]);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('data.avatar_url'));
        $this->assertSame(1, $user->fresh()->getMedia('avatar')->count());
    }

    public function test_update_profile_accepts_a_heic_avatar_from_iphone_cameras(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->post('/api/v1/mobile/profile', [
                'name'    => $user->name,
                'phone'   => $user->phone,
                'avatar'  => UploadedFile::fake()->create('photo.heic', 500, 'image/heic'),
                '_method' => 'PUT',
            ]);

        $response->assertStatus(200);
        $this->assertSame(1, $user->fresh()->getMedia('avatar')->count());
    }

    public function test_update_profile_replaces_the_previous_avatar(): void
    {
        $user = $this->actingUser();
        $token = $this->tokenFor($user);
        $this->withHeader('Authorization', "Bearer {$token}")->post('/api/v1/mobile/profile', [
            'name' => $user->name, 'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->image('first.jpg'), '_method' => 'PUT',
        ]);

        $this->withHeader('Authorization', "Bearer {$token}")->post('/api/v1/mobile/profile', [
            'name' => $user->name, 'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->image('second.jpg'), '_method' => 'PUT',
        ]);

        $this->assertSame(1, $user->fresh()->getMedia('avatar')->count());
    }

    public function test_update_profile_rejects_a_non_image_avatar(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->post('/api/v1/mobile/profile', [
                'name'    => $user->name,
                'phone'   => $user->phone,
                'avatar'  => UploadedFile::fake()->create('doc.pdf', 500, 'application/pdf'),
                '_method' => 'PUT',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['avatar']);
    }

    public function test_update_profile_rejects_an_oversized_avatar(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->post('/api/v1/mobile/profile', [
                'name'    => $user->name,
                'phone'   => $user->phone,
                'avatar'  => UploadedFile::fake()->image('big.jpg')->size(3000),
                '_method' => 'PUT',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['avatar']);
    }

    public function test_change_password_succeeds_with_correct_current_password(): void
    {
        $user = $this->actingUser();
        $user->update(['password' => 'OldPassword123']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson('/api/v1/mobile/profile/password', [
                'current_password'      => 'OldPassword123',
                'password'              => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $user = $this->actingUser();
        $user->update(['password' => 'OldPassword123']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson('/api/v1/mobile/profile/password', [
                'current_password'      => 'WrongPassword',
                'password'              => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response->assertStatus(400)->assertJsonPath('success', false);
    }

    public function test_update_preferences_persists_theme_and_notification_flag(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson('/api/v1/mobile/profile/preferences', [
                'notification_enabled' => false,
                'theme'                => 'dark',
            ]);

        $response->assertStatus(200)->assertJsonPath('data.theme', 'dark');
    }

    public function test_update_preferences_validation_rejects_invalid_theme(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->putJson('/api/v1/mobile/profile/preferences', [
                'notification_enabled' => true,
                'theme'                => 'not-a-real-theme',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['theme']);
    }

    public function test_delete_account_soft_deletes_the_user(): void
    {
        $user = $this->actingUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->deleteJson('/api/v1/mobile/profile');

        $response->assertStatus(200)->assertJsonPath('success', true);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_accept_policy_succeeds_even_before_policy_is_accepted(): void
    {
        $user = $this->actingUser(policyAccepted: false);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenFor($user))
            ->postJson('/api/v1/mobile/profile/accept-policy');

        $response->assertStatus(200)->assertJsonPath('data.policy_accepted', true);
    }
}

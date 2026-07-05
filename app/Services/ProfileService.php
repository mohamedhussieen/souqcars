<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

/** Handles all profile-related business logic: updates, password changes, preferences, deletion. */
class ProfileService
{
    /** Updates the user's name, phone, and optionally replaces the avatar via Spatie Media Library. */
    public function update(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        $user->update([
            'name'  => $data['name'],
            'phone' => $data['phone'],
        ]);

        if ($avatar) {
            $user->addMedia($avatar)->toMediaCollection('avatar');
        }

        return $user->fresh();
    }

    /** Verifies the current password and sets a new one; returns false if verification fails. */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update(['password' => $newPassword]);

        return true;
    }

    /** Updates the user's notification and theme preferences. */
    public function updatePreferences(User $user, bool $notificationEnabled, string $theme): User
    {
        $user->update([
            'notification_enabled' => $notificationEnabled,
            'theme'                => $theme,
        ]);

        return $user->fresh();
    }

    /** Soft-deletes the user account and revokes all their Sanctum tokens. */
    public function deleteAccount(User $user): void
    {
        $user->tokens()->delete();
        $user->delete();
    }

    /** Records the user's acceptance of the application policy. */
    public function acceptPolicy(User $user): User
    {
        $user->update(['policy_accepted_at' => now()]);

        return $user->fresh();
    }
}

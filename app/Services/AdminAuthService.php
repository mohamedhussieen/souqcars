<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/** Handles authentication for the admin dashboard, restricted to admin-role users. */
class AdminAuthService
{
    /** Verifies admin credentials and returns the user with a fresh Sanctum token, or null on failure. */
    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        if (!$user->hasRole(UserRole::Admin->value)) {
            return null;
        }

        if (!$user->is_active) {
            return ['inactive' => true];
        }

        $token = $user->createToken('admin-dashboard', expiresAt: now()->addDays(7))->plainTextToken;

        return compact('user', 'token');
    }

    /** Revokes only the current Sanctum token used for this request. */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}

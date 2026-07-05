<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles all admin-dashboard operations for managing marketplace users. */
class AdminUserService
{
    /** Returns a paginated, optionally filtered list of users ordered by newest first. */
    public function list(int $perPage, ?string $search, ?string $role): LengthAwarePaginator
    {
        return User::query()
            ->when($search, fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->when($role, fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', $role)))
            ->latest()
            ->paginate($perPage);
    }

    /** Toggles the user's active status and revokes their tokens when deactivated. */
    public function toggleActive(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);

        if (!$user->is_active) {
            $user->tokens()->delete();
        }

        return $user->fresh();
    }

    /** Replaces the user's role with the given one. */
    public function updateRole(User $user, UserRole $role): User
    {
        $user->syncRoles([$role->value]);

        return $user->fresh();
    }

    /** Soft-deletes the user account and revokes all their Sanctum tokens. */
    public function delete(User $user): void
    {
        $user->tokens()->delete();
        $user->delete();
    }
}

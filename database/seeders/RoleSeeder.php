<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/** Seeds the three Spatie Permission roles required by the marketplace. */
class RoleSeeder extends Seeder
{
    /** Creates admin, showroom_owner, and user roles idempotently. */
    public function run(): void
    {
        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(
                ['name' => $role->value, 'guard_name' => 'sanctum']
            );
        }
    }
}

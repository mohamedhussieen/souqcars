<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Seeds a default admin account for accessing the dashboard. */
class AdminUserSeeder extends Seeder
{
    /** Creates one admin user idempotently, with the policy pre-accepted. */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@cars-marketplace.test'],
            [
                'name'                => 'Marketplace Admin',
                'phone'               => '01000000000',
                'password'            => 'admin12345',
                'policy_accepted_at'  => now(),
            ]
        );

        $admin->syncRoles([UserRole::Admin->value]);
    }
}

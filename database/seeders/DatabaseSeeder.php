<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Root seeder that orchestrates all seeder execution order. */
class DatabaseSeeder extends Seeder
{
    /** Runs all seeders in dependency order: roles → cities → brands → app config → admin user. */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CitySeeder::class,
            BrandSeeder::class,
            AppConfigSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}

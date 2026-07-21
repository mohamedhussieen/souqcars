<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Seeds only the reference data a production database needs: roles, cities, brands+models, colors. No test users, cars, or bookings. */
class ProductionSeeder extends Seeder
{
    /** Runs the production-safe seeders in dependency order. */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CitySeeder::class,
            BrandSeeder::class,
            ColorSeeder::class,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake maintenance services for testing. */
class MaintenanceServiceFactory extends Factory
{
    protected $model = MaintenanceService::class;

    /** Defines the default maintenance service attribute set. */
    public function definition(): array
    {
        return [
            'maintenance_center_id' => MaintenanceCenter::factory(),
            'name_ar'               => 'خدمة ' . fake()->unique()->word(),
            'name_en'               => fake()->unique()->words(2, true) . ' Service',
            'price'                 => fake()->randomFloat(2, 50, 2000),
            'is_active'             => true,
            'sort_order'            => 0,
        ];
    }
}

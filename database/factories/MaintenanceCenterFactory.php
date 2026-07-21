<?php

namespace Database\Factories;

use App\Models\MaintenanceCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake maintenance centers for testing. */
class MaintenanceCenterFactory extends Factory
{
    protected $model = MaintenanceCenter::class;

    /** Defines the default maintenance center attribute set. */
    public function definition(): array
    {
        return [
            'name_ar'   => 'مركز صيانة ' . fake()->unique()->word(),
            'name_en'   => fake()->unique()->company() . ' Service Center',
            'phone'     => fake()->unique()->numerify('010########'),
            'rating'    => fake()->randomFloat(2, 0, 5),
            'is_active' => true,
        ];
    }
}

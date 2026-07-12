<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake bilingual car models for testing. */
class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    /** Defines the default car model attribute set. */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'name_ar'  => 'موديل ' . fake()->unique()->word(),
            'name_en'  => 'Model ' . fake()->unique()->word(),
        ];
    }
}

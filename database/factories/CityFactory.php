<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake bilingual cities for testing. */
class CityFactory extends Factory
{
    protected $model = City::class;

    /** Defines the default city attribute set. */
    public function definition(): array
    {
        return [
            'name_ar' => 'مدينة ' . fake()->unique()->word(),
            'name_en' => fake()->unique()->city(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake bilingual colors for testing. */
class ColorFactory extends Factory
{
    protected $model = Color::class;

    /** Defines the default color attribute set. */
    public function definition(): array
    {
        return [
            'name_ar'   => 'لون ' . fake()->unique()->word(),
            'name_en'   => fake()->unique()->colorName(),
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Showroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake showroom profiles for testing. */
class ShowroomFactory extends Factory
{
    protected $model = Showroom::class;

    /** Defines the default showroom attribute set. */
    public function definition(): array
    {
        return [
            'name_ar'     => 'معرض ' . fake()->unique()->word(),
            'name_en'     => fake()->unique()->company(),
            'phone'       => fake()->unique()->numerify('010########'),
            'rating'      => fake()->randomFloat(2, 0, 5),
            'is_verified' => false,
            'is_active'   => true,
        ];
    }
}

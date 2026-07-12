<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake users for testing. */
class UserFactory extends Factory
{
    protected $model = User::class;

    /** Defines the default user attribute set. */
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'phone'    => fake()->unique()->numerify('010########'),
            'password' => 'Password123',
        ];
    }
}

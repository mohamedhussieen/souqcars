<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake bilingual car brands for testing. */
class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /** Defines the default brand attribute set. */
    public function definition(): array
    {
        return [
            'name_ar' => 'ماركة ' . fake()->unique()->word(),
            'name_en' => 'Brand ' . fake()->unique()->word(),
        ];
    }
}

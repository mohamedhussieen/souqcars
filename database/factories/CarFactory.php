<?php

namespace Database\Factories;

use App\Enums\BodyType;
use App\Enums\CarCondition;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\PaymentType;
use App\Enums\SellerType;
use App\Enums\Transmission;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Generates fake car listings for testing (admin-seller, active by default). */
class CarFactory extends Factory
{
    protected $model = Car::class;

    /** Defines the default car attribute set. */
    public function definition(): array
    {
        return [
            'seller_type'   => SellerType::Admin,
            'seller_id'     => null,
            'brand_id'      => Brand::factory(),
            'car_model_id'  => CarModel::factory(),
            'city_id'       => City::factory(),
            'color_id'      => null,
            'year'          => fake()->numberBetween(2010, 2026),
            'title_ar'      => 'سيارة ' . fake()->words(2, true),
            'title_en'      => 'Car ' . fake()->words(2, true),
            'price'         => fake()->numberBetween(100000, 3000000),
            'payment_type'  => PaymentType::Cash,
            'mileage'       => fake()->numberBetween(0, 250000),
            'condition'     => CarCondition::Used,
            'transmission'  => Transmission::Automatic,
            'fuel_type'     => FuelType::Petrol,
            'body_type'     => BodyType::Sedan,
            'owners_count'  => 1,
            'status'        => CarStatus::Active,
        ];
    }

    /** State: pending listing. */
    public function pending(): static
    {
        return $this->state(['status' => CarStatus::Pending]);
    }

    /** State: sold listing. */
    public function sold(): static
    {
        return $this->state(['status' => CarStatus::Sold]);
    }
}

<?php

namespace Database\Seeders;

use App\Enums\BodyType;
use App\Enums\CarCondition;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\PaymentType;
use App\Enums\SellerType;
use App\Enums\Transmission;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\City;
use App\Models\Color;
use App\Models\Showroom;
use App\Models\User;
use App\Services\CarService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Seeds a realistic set of car listings through CarService::create() — the same path the
 * admin dashboard uses — so every listing has full specs, a seller, and gallery images.
 */
class CarSeeder extends Seeder
{
    /** Runs the car listing seed: a mix of admin, individual, and showroom sellers across every status. */
    public function run(): void
    {
        $carService = app(CarService::class);

        $brands = Brand::with('carModels')->get();
        $cities = City::all();
        $colors = Color::all();

        if ($brands->isEmpty() || $cities->isEmpty()) {
            $this->command?->warn('CarSeeder skipped: run BrandSeeder and CitySeeder first.');

            return;
        }

        $individualSellers = User::factory()->count(5)->create([
            'password' => 'Password123',
        ]);

        $showroom = Showroom::first() ?? Showroom::factory()->create();

        $statuses = [
            CarStatus::Active,
            CarStatus::Active,
            CarStatus::Active,
            CarStatus::Active,
            CarStatus::Pending,
            CarStatus::NeedsInspection,
            CarStatus::Rejected,
            CarStatus::Sold,
        ];

        $sellers = [
            ['type' => SellerType::Admin, 'id' => null],
            ['type' => SellerType::Showroom, 'id' => $showroom->id],
            ...$individualSellers->map(fn (User $user) => ['type' => SellerType::Individual, 'id' => $user->id])->all(),
        ];

        $totalToCreate = 40;

        for ($i = 0; $i < $totalToCreate; $i++) {
            $brand = $brands->random();
            $model = $brand->carModels->isNotEmpty() ? $brand->carModels->random() : null;

            if ($model === null) {
                continue;
            }

            $seller = $sellers[$i % count($sellers)];
            $status = $statuses[$i % count($statuses)];

            $data = [
                'seller_type'     => $seller['type'],
                'seller_id'       => $seller['id'],
                'brand_id'        => $brand->id,
                'car_model_id'    => $model->id,
                'city_id'         => $cities->random()->id,
                'color_id'        => $colors->isNotEmpty() ? $colors->random()->id : null,
                'year'            => fake()->numberBetween(2012, 2026),
                'title_ar'        => "{$brand->name_ar} {$model->name_ar} " . fake()->numberBetween(2012, 2026),
                'title_en'        => "{$brand->name_en} {$model->name_en} " . fake()->numberBetween(2012, 2026),
                'description_ar'  => 'سيارة بحالة ممتازة، تم صيانتها بانتظام ولا يوجد بها أي أعطال. السعر قابل للتفاوض البسيط.',
                'description_en'  => 'Excellent condition car, regularly maintained with no mechanical issues. Slightly negotiable price.',
                'price'           => fake()->numberBetween(150000, 3500000),
                'payment_type'    => fake()->randomElement(PaymentType::cases()),
                'mileage'         => fake()->numberBetween(0, 220000),
                'condition'       => fake()->randomElement(CarCondition::cases()),
                'transmission'    => fake()->randomElement(Transmission::cases()),
                'fuel_type'       => fake()->randomElement(FuelType::cases()),
                'body_type'       => fake()->randomElement(BodyType::cases()),
                'owners_count'    => fake()->numberBetween(1, 4),
                'status'          => $status,
                'rejection_reason' => $status === CarStatus::Rejected ? 'الصور المرفقة غير واضحة، يرجى إعادة رفع صور أوضح للسيارة.' : null,
                'is_featured'     => $i % 7 === 0,
                'views_count'     => fake()->numberBetween(0, 5000),
                'favorites_count' => 0,
            ];

            $images = collect(range(1, fake()->numberBetween(2, 4)))
                ->map(fn () => $this->placeholderImage())
                ->all();

            $car = $carService->create($data, $images);

            if ($status === CarStatus::NeedsInspection || fake()->boolean(30)) {
                $carService->uploadInspectionReport($car, $this->placeholderImage('inspection'));
            }
        }
    }

    /** Generates a small in-memory JPEG so seeded cars have real gallery images without any network calls. */
    private function placeholderImage(string $prefix = 'car'): UploadedFile
    {
        $image = imagecreatetruecolor(640, 480);
        $color = imagecolorallocate(
            $image,
            fake()->numberBetween(0, 255),
            fake()->numberBetween(0, 255),
            fake()->numberBetween(0, 255),
        );
        imagefill($image, 0, 0, $color);

        $path = sys_get_temp_dir() . '/' . $prefix . '-' . Str::random(8) . '.jpg';
        imagejpeg($image, $path, 80);
        imagedestroy($image);

        return new UploadedFile($path, basename($path), 'image/jpeg', null, true);
    }
}

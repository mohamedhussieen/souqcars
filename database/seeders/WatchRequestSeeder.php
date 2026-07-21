<?php

namespace Database\Seeders;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\User;
use App\Services\WatchRequestService;
use Illuminate\Database\Seeder;

/** Seeds sample "notify me" watch requests against the sold cars created by CarSeeder. */
class WatchRequestSeeder extends Seeder
{
    /** Creates a watch request per user for a random sold car's brand/model; skips if any already exist. */
    public function run(): void
    {
        if (\App\Models\CarWatchRequest::query()->exists()) {
            return;
        }

        $soldCars = Car::query()->where('status', CarStatus::Sold)->get();
        $users = User::whereDoesntHave('roles')->limit(5)->get();

        if ($soldCars->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('WatchRequestSeeder skipped: run CarSeeder first to create sold cars and individual users.');

            return;
        }

        $watchRequestService = app(WatchRequestService::class);

        foreach ($users as $index => $user) {
            $car = $soldCars[$index % $soldCars->count()];

            $watchRequestService->watch($user, $car);
        }
    }
}

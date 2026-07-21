<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Seeds a spread of bookings across every status for the individual users created by CarSeeder. */
class BookingSeeder extends Seeder
{
    /** Inserts sample bookings idempotently (guarded by an existence check since bookings have no natural unique key). */
    public function run(): void
    {
        if (Booking::query()->exists()) {
            return;
        }

        $centers = MaintenanceCenter::with('services')->get();
        $users = User::whereDoesntHave('roles')->limit(5)->get();

        if ($centers->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('BookingSeeder skipped: run MaintenanceCenterSeeder and CarSeeder (for users) first.');

            return;
        }

        $statuses = [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::Completed, BookingStatus::Cancelled];

        foreach ($users as $index => $user) {
            $center = $centers->random();
            $service = $center->services->isNotEmpty() ? $center->services->random() : null;

            if ($service === null) {
                continue;
            }

            $status = $statuses[$index % count($statuses)];

            Booking::create([
                'user_id'                => $user->id,
                'maintenance_center_id'  => $center->id,
                'maintenance_service_id' => $service->id,
                'status'                 => $status->value,
                'date'                   => $status === BookingStatus::Completed
                    ? now()->subDays(fake()->numberBetween(2, 30))->toDateString()
                    : now()->addDays(fake()->numberBetween(1, 14))->toDateString(),
                'time'                   => fake()->randomElement(['09:00:00', '11:30:00', '14:00:00', '16:30:00']),
                'price'                  => $service->price,
                'notes'                  => fake()->boolean(50) ? 'برجاء الفحص الجيد قبل التسليم' : null,
                'cancellation_reason'    => $status === BookingStatus::Cancelled ? 'تغيرت المواعيد المتاحة لدي' : null,
            ]);
        }
    }
}

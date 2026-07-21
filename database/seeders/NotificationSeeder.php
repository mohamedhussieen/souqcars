<?php

namespace Database\Seeders;

use App\Enums\NotificationType;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;

/** Seeds a few sample notifications (mixed read/unread) for the individual users created by CarSeeder. */
class NotificationSeeder extends Seeder
{
    /** Sends 2-3 sample notifications to each non-admin user; skips if notifications already exist. */
    public function run(): void
    {
        if (\App\Models\Notification::query()->exists()) {
            return;
        }

        $users = User::whereDoesntHave('roles')->limit(5)->get();

        if ($users->isEmpty()) {
            $this->command?->warn('NotificationSeeder skipped: run CarSeeder first to create individual users.');

            return;
        }

        $notificationService = app(NotificationService::class);
        $types = [NotificationType::CarMatch, NotificationType::PriceDrop, NotificationType::CarAvailable];

        foreach ($users as $index => $user) {
            foreach ($types as $typeIndex => $type) {
                $notification = $notificationService->send($user, $type);

                // Mark the first notification of every other user as already read, for a realistic mix.
                if ($index % 2 === 0 && $typeIndex === 0) {
                    $notification->update(['read_at' => now()->subHours(3)]);
                }
            }
        }
    }
}

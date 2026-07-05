<?php

namespace Database\Seeders;

use App\Enums\Platform;
use App\Models\AppConfig;
use Illuminate\Database\Seeder;

/** Seeds default maintenance/upgrade configuration for each supported platform. */
class AppConfigSeeder extends Seeder
{
    /** Creates one app-config row per platform idempotently. */
    public function run(): void
    {
        foreach (Platform::cases() as $platform) {
            AppConfig::firstOrCreate(
                ['platform' => $platform->value],
                [
                    'maintenance_enabled' => false,
                    'maintenance_message' => 'التطبيق قيد الصيانة حالياً. يرجى المحاولة مرة أخرى لاحقاً.',
                    'min_version'         => '1.0.0',
                    'current_version'     => '1.0.0',
                    'force_upgrade'       => false,
                    'upgrade_message'     => 'يتوفر إصدار جديد. يرجى تحديث التطبيق للمتابعة.',
                ]
            );
        }
    }
}

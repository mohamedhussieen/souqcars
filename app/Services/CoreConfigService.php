<?php

namespace App\Services;

use App\Enums\Platform;
use App\Models\AppConfig;

/** Resolves maintenance and upgrade status for the requesting app version. */
class CoreConfigService
{
    /** Builds the maintenance/upgrade payload for the given platform and client version. */
    public function getConfig(Platform $platform, string $clientVersion): array
    {
        $config = AppConfig::where('platform', $platform->value)->firstOrFail();

        return [
            'maintenance' => [
                'enabled' => $config->maintenance_enabled,
                'message' => $config->maintenance_message,
            ],
            'upgrade' => [
                'force'           => $config->force_upgrade,
                'current_version' => $config->current_version,
                'min_version'     => $config->min_version,
                'is_outdated'     => version_compare($clientVersion, $config->min_version, '<'),
                'message'         => $config->upgrade_message,
            ],
        ];
    }
}

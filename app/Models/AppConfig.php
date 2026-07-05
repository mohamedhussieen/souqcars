<?php

namespace App\Models;

use App\Enums\Platform;
use Illuminate\Database\Eloquent\Model;

/** Stores maintenance and upgrade configuration for a single client platform. */
class AppConfig extends Model
{
    protected $fillable = [
        'platform',
        'maintenance_enabled',
        'maintenance_message',
        'min_version',
        'current_version',
        'force_upgrade',
        'upgrade_message',
    ];

    protected function casts(): array
    {
        return [
            'platform'             => Platform::class,
            'maintenance_enabled'  => 'boolean',
            'force_upgrade'        => 'boolean',
        ];
    }
}

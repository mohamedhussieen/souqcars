<?php

namespace Tests\Unit;

use App\Enums\Platform;
use App\Models\AppConfig;
use App\Services\CoreConfigService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies CoreConfigService builds the correct maintenance/upgrade payload per platform and version. */
class CoreConfigServiceTest extends TestCase
{
    use RefreshDatabase;

    private CoreConfigService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CoreConfigService();
    }

    public function test_get_config_returns_maintenance_and_upgrade_payload(): void
    {
        AppConfig::create([
            'platform'             => Platform::Android->value,
            'maintenance_enabled'  => true,
            'maintenance_message'  => 'Down for maintenance',
            'min_version'          => '2.0.0',
            'current_version'      => '2.1.0',
            'force_upgrade'        => false,
            'upgrade_message'      => 'Update available',
        ]);

        $config = $this->service->getConfig(Platform::Android, '2.1.0');

        $this->assertTrue($config['maintenance']['enabled']);
        $this->assertSame('Down for maintenance', $config['maintenance']['message']);
        $this->assertFalse($config['upgrade']['force']);
        $this->assertSame('2.0.0', $config['upgrade']['min_version']);
    }

    public function test_get_config_flags_outdated_client_version(): void
    {
        AppConfig::create([
            'platform'        => Platform::Ios->value,
            'min_version'     => '3.0.0',
            'current_version' => '3.2.0',
        ]);

        $config = $this->service->getConfig(Platform::Ios, '2.5.0');

        $this->assertTrue($config['upgrade']['is_outdated']);
    }

    public function test_get_config_does_not_flag_up_to_date_client_version(): void
    {
        AppConfig::create([
            'platform'        => Platform::Ios->value,
            'min_version'     => '3.0.0',
            'current_version' => '3.2.0',
        ]);

        $config = $this->service->getConfig(Platform::Ios, '3.1.0');

        $this->assertFalse($config['upgrade']['is_outdated']);
    }

    public function test_get_config_throws_when_platform_has_no_row(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->getConfig(Platform::Android, '1.0.0');
    }
}

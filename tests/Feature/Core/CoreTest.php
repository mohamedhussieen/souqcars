<?php

namespace Tests\Feature\Core;

use App\Enums\Platform;
use App\Models\AppConfig;
use App\Models\PolicyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies the public core endpoints: app-config maintenance/upgrade gate and terms & conditions. */
class CoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_config_returns_maintenance_and_upgrade_payload(): void
    {
        AppConfig::create([
            'platform'        => Platform::Android->value,
            'min_version'     => '1.0.0',
            'current_version' => '1.2.0',
        ]);

        $response = $this->getJson('/api/core/app-config?platform=android&version=1.0.0');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['maintenance', 'upgrade']]);
    }

    public function test_app_config_validation_rejects_unknown_platform(): void
    {
        $response = $this->getJson('/api/core/app-config?platform=windows&version=1.0.0');

        $response->assertStatus(422);
    }

    public function test_app_config_returns_404_when_platform_has_no_row(): void
    {
        $response = $this->getJson('/api/core/app-config?platform=ios&version=1.0.0');

        $response->assertStatus(404);
    }

    public function test_terms_returns_ordered_policy_terms(): void
    {
        PolicyTerm::create(['order' => 1, 'title_ar' => 'أ', 'title_en' => 'A', 'body_ar' => 'نص', 'body_en' => 'Body']);

        $response = $this->getJson('/api/core/terms');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_terms_localizes_title_by_accept_language_header(): void
    {
        PolicyTerm::create(['order' => 1, 'title_ar' => 'الشروط', 'title_en' => 'Terms', 'body_ar' => 'نص', 'body_en' => 'Body']);

        $arResponse = $this->withHeader('Accept-Language', 'ar')->getJson('/api/core/terms');
        $enResponse = $this->withHeader('Accept-Language', 'en')->getJson('/api/core/terms');

        $this->assertSame('الشروط', $arResponse->json('data.0.title'));
        $this->assertSame('Terms', $enResponse->json('data.0.title'));
    }

    public function test_app_config_language_param_overrides_the_response_message_locale(): void
    {
        AppConfig::create([
            'platform'        => Platform::Android->value,
            'min_version'     => '1.0.0',
            'current_version' => '1.0.0',
        ]);

        $arResponse = $this->getJson('/api/core/app-config?platform=android&version=1.0.0&language=ar');
        $enResponse = $this->getJson('/api/core/app-config?platform=android&version=1.0.0&language=en');

        $this->assertNotSame($arResponse->json('message'), $enResponse->json('message'));
    }
}

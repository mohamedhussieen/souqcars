<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Ad;
use App\Models\Showroom;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Verifies the admin ads CRUD endpoints and the single-row showroom profile endpoints. */
class AdminAdsAndShowroomTest extends TestCase
{
    use RefreshDatabase;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::Admin->value);
        $this->adminToken = $admin->createToken('admin-dashboard')->plainTextToken;
    }

    private function asAdmin()
    {
        return $this->withHeader('Authorization', "Bearer {$this->adminToken}");
    }

    // Ads

    public function test_list_ads_returns_paginated_ads(): void
    {
        Ad::create(['type' => 'banner', 'sort_order' => 0]);

        $response = $this->asAdmin()->getJson('/api/admin/ads');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_store_ad_creates_a_new_ad(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/ads', ['type' => 'banner', 'title_en' => 'Sale']);

        $response->assertStatus(201);
        $this->assertDatabaseHas('ads', ['title_en' => 'Sale']);
    }

    public function test_store_ad_validation_rejects_ends_before_starts(): void
    {
        $response = $this->asAdmin()->postJson('/api/admin/ads', [
            'type'       => 'banner',
            'starts_at'  => '2026-06-01',
            'ends_at'    => '2026-05-01',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['ends_at']);
    }

    public function test_store_ad_accepts_an_image_upload(): void
    {
        $response = $this->asAdmin()->post('/api/admin/ads', [
            'type'  => 'banner',
            'image' => UploadedFile::fake()->image('ad.jpg'),
        ]);

        $response->assertStatus(201);
    }

    public function test_update_ad_changes_fields(): void
    {
        $ad = Ad::create(['type' => 'banner', 'title_en' => 'Old', 'sort_order' => 0]);

        $response = $this->asAdmin()->putJson("/api/admin/ads/{$ad->id}", ['title_en' => 'New']);

        $response->assertStatus(200);
        $this->assertSame('New', $ad->fresh()->title_en);
    }

    public function test_delete_ad_removes_it(): void
    {
        $ad = Ad::create(['type' => 'banner', 'sort_order' => 0]);

        $response = $this->asAdmin()->deleteJson("/api/admin/ads/{$ad->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('ads', ['id' => $ad->id]);
    }

    public function test_ads_endpoints_require_admin_role(): void
    {
        $response = $this->getJson('/api/admin/ads');

        $response->assertStatus(401);
    }

    // Showroom

    public function test_show_showroom_lazily_creates_the_default_row(): void
    {
        $response = $this->asAdmin()->getJson('/api/admin/showroom');

        $response->assertStatus(200);
        $this->assertDatabaseCount('showrooms', 1);
    }

    public function test_update_showroom_changes_fields(): void
    {
        $response = $this->asAdmin()->putJson('/api/admin/showroom', ['name_en' => 'New Showroom Name']);

        $response->assertStatus(200)->assertJsonPath('data.name_en', 'New Showroom Name');
    }

    public function test_update_showroom_validation_rejects_invalid_latitude(): void
    {
        $response = $this->asAdmin()->putJson('/api/admin/showroom', ['lat' => 999]);

        $response->assertStatus(422)->assertJsonValidationErrors(['lat']);
    }

    public function test_upload_showroom_logo_stores_the_file(): void
    {
        $response = $this->asAdmin()->post('/api/admin/showroom/logo', [
            'logo' => UploadedFile::fake()->image('logo.jpg'),
        ]);

        $response->assertStatus(200)->assertJsonStructure(['data' => ['logo_url']]);
    }

    public function test_showroom_endpoints_require_admin_role(): void
    {
        $response = $this->getJson('/api/admin/showroom');

        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Ad;
use App\Services\AdService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/** Verifies AdService active-ad filtering, listing, creation, update, and deletion. */
class AdServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdService();
    }

    private function adData(array $overrides = []): array
    {
        return array_merge([
            'title_ar'    => 'إعلان',
            'title_en'    => 'Ad',
            'type'        => 'banner',
            'is_active'   => true,
            'sort_order'  => 0,
        ], $overrides);
    }

    public function test_active_ads_excludes_inactive_ads(): void
    {
        Ad::create($this->adData(['is_active' => true]));
        Ad::create($this->adData(['is_active' => false]));

        $result = $this->service->activeAds();

        $this->assertCount(1, $result);
    }

    public function test_active_ads_excludes_ads_outside_date_range(): void
    {
        Ad::create($this->adData(['starts_at' => now()->addDays(5)]));
        Ad::create($this->adData(['ends_at' => now()->subDays(5)]));
        $current = Ad::create($this->adData(['starts_at' => now()->subDay(), 'ends_at' => now()->addDay()]));

        $result = $this->service->activeAds();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($current));
    }

    public function test_active_ads_includes_open_ended_ads_with_null_dates(): void
    {
        Ad::create($this->adData());

        $result = $this->service->activeAds();

        $this->assertCount(1, $result);
    }

    public function test_active_ads_orders_by_sort_order(): void
    {
        Ad::create($this->adData(['title_en' => 'Second', 'sort_order' => 2]));
        Ad::create($this->adData(['title_en' => 'First', 'sort_order' => 1]));

        $result = $this->service->activeAds();

        $this->assertSame('First', $result->first()->title_en);
    }

    public function test_list_returns_paginated_ads(): void
    {
        Ad::create($this->adData(['is_active' => false]));
        Ad::create($this->adData());

        $result = $this->service->list(10);

        $this->assertSame(2, $result->total());
    }

    public function test_create_persists_a_new_ad(): void
    {
        $ad = $this->service->create($this->adData(['title_en' => 'Brand New']));

        $this->assertDatabaseHas('ads', ['title_en' => 'Brand New']);
        $this->assertSame('Brand New', $ad->title_en);
    }

    public function test_create_attaches_image_when_provided(): void
    {
        $image = UploadedFile::fake()->image('ad.jpg');

        $ad = $this->service->create($this->adData(), $image);

        $this->assertSame(1, $ad->getMedia('ad_image')->count());
    }

    public function test_update_changes_ad_fields(): void
    {
        $ad = Ad::create($this->adData(['title_en' => 'Old']));

        $updated = $this->service->update($ad, ['title_en' => 'Updated']);

        $this->assertSame('Updated', $updated->title_en);
    }

    public function test_delete_removes_the_ad(): void
    {
        $ad = Ad::create($this->adData());

        $this->service->delete($ad);

        $this->assertDatabaseMissing('ads', ['id' => $ad->id]);
    }
}

<?php

namespace App\Services;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

/** Handles admin-dashboard CRUD for promotional ads/banners. */
class AdService
{
    /** Returns all currently active ads within their date range (open-ended when dates are null), ordered by sort_order. */
    public function activeAds(): Collection
    {
        return Ad::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()))
            ->orderBy('sort_order')
            ->get();
    }

    /** Returns a paginated list of all ads ordered by sort_order. */
    public function list(int $perPage): LengthAwarePaginator
    {
        return Ad::query()->orderBy('sort_order')->paginate($perPage);
    }

    /** Creates a new ad and optionally attaches its image via Media Library. */
    public function create(array $data, ?UploadedFile $imageFile = null): Ad
    {
        $ad = Ad::create($data);

        if ($imageFile) {
            $ad->addMedia($imageFile)->toMediaCollection('ad_image');
        }

        return $ad->fresh();
    }

    /** Updates the given ad and optionally replaces its image. */
    public function update(Ad $ad, array $data, ?UploadedFile $imageFile = null): Ad
    {
        $ad->update($data);

        if ($imageFile) {
            $ad->addMedia($imageFile)->toMediaCollection('ad_image');
        }

        return $ad->fresh();
    }

    /** Deletes the given ad along with its media. */
    public function delete(Ad $ad): void
    {
        $ad->delete();
    }
}

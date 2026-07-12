<?php

namespace App\Services;

use App\Models\Showroom;
use Illuminate\Http\UploadedFile;

/** Handles the single admin-managed showroom profile (Phase 1: exactly one row). */
class ShowroomService
{
    /** Returns the single showroom row, lazily creating a sensible default if none exists yet. */
    public function get(): Showroom
    {
        return Showroom::query()->firstOrCreate([], [
            'name_ar' => 'المعرض الرئيسي',
            'name_en' => 'Main Showroom',
            'phone'   => '00000000000',
        ]);
    }

    /** Updates the showroom profile and optionally replaces its logo. */
    public function update(Showroom $showroom, array $data, ?UploadedFile $logoFile = null): Showroom
    {
        $showroom->update($data);

        if ($logoFile) {
            $showroom->addMedia($logoFile)->toMediaCollection('logo');
        }

        return $showroom->fresh();
    }
}

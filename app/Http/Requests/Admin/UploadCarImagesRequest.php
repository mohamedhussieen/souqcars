<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the multi-image upload payload for a car's gallery. */
class UploadCarImagesRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires an array of up to 10 image files, each at most 5MB. */
    public function rules(): array
    {
        return [
            'images'   => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
        ];
    }
}

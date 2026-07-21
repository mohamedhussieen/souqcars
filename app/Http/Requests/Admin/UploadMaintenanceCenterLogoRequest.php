<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates a maintenance center logo upload. */
class UploadMaintenanceCenterLogoRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires an image file under 2MB. */
    public function rules(): array
    {
        return [
            'logo' => ['required', 'file', 'max:2048', 'mimetypes:image/jpeg,image/png,image/webp'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}

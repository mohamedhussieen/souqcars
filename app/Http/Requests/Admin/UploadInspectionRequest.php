<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the inspection report upload payload for a car. */
class UploadInspectionRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires a single file (image or PDF) of at most 10MB. */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:jpeg,png,pdf', 'max:10240'],
        ];
    }
}

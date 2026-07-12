<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates updates to a color lookup entity — all fields optional. */
class UpdateColorRequest extends FormRequest
{
    /** Authorization is enforced by the EnsureIsAdmin middleware. */
    public function authorize(): bool
    {
        return true;
    }

    /** All fields are optional (partial update semantics). */
    public function rules(): array
    {
        return [
            'name_ar'   => ['sometimes', 'string', 'max:255'],
            'name_en'   => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

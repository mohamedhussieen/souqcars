<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Validates admin updates to the single showroom profile. */
class UpdateShowroomRequest extends FormRequest
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
            'name_ar'     => ['sometimes', 'string', 'max:255'],
            'name_en'     => ['sometimes', 'string', 'max:255'],
            'phone'       => ['sometimes', 'string', 'max:50'],
            'whatsapp'    => ['sometimes', 'nullable', 'string', 'max:50'],
            'email'       => ['sometimes', 'nullable', 'email', 'max:255'],
            'address_ar'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_en'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'lat'         => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng'         => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'is_verified' => ['sometimes', 'boolean'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}

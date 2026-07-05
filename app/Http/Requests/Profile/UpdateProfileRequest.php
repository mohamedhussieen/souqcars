<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validates profile update data including optional avatar upload. */
class UpdateProfileRequest extends FormRequest
{
    /** Only authenticated users may update their profile. */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** Validates name, phone uniqueness (excluding self), and optional image avatar. */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name'   => ['required', 'string', 'max:255'],
            'phone'  => ['required', 'string', Rule::unique('users', 'phone')->ignore($userId)],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}

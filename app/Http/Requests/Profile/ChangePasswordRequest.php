<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

/** Validates a password change request, ensuring the current password is provided. */
class ChangePasswordRequest extends FormRequest
{
    /** Only authenticated users may change their password. */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /** Requires current password plus a new confirmed password of at least 8 characters. */
    public function rules(): array
    {
        return [
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}

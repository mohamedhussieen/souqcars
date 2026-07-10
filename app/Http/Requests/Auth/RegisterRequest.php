<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validates incoming user registration data. */
class RegisterRequest extends FormRequest
{
    /** All registration endpoints are public; no auth check needed. */
    public function authorize(): bool
    {
        return true;
    }

    /** Registration validation rules enforcing unique email and phone. */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'phone'                 => ['required', 'string', Rule::unique('users', 'phone')->whereNull('deleted_at')],
            'email'                 => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'fcm_token'             => ['sometimes', 'nullable', 'string'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }

    /** Localized validation messages. */
    public function messages(): array
    {
        return trans('validation') ?? [];
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/** Validates user login credentials. */
class LoginRequest extends FormRequest
{
    /** Login is a public endpoint; no auth check needed. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires valid email format and non-empty password. */
    public function rules(): array
    {
        return [
            'email'     => ['required', 'email'],
            'password'  => ['required', 'string'],
            'fcm_token' => ['sometimes', 'nullable', 'string'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the email before sending a password reset link. */
class ForgotPasswordRequest extends FormRequest
{
    /** Forgot password is a public endpoint. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires a valid email address that exists in the users table. */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}

<?php

namespace App\Http\Requests\Auth\PasswordReset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/** Validates the email, reset token, and new password before completing the reset. */
class ResetPasswordRequest extends FormRequest
{
    /** Password reset is a public endpoint. */
    public function authorize(): bool
    {
        return true;
    }

    /** Requires email, the reset token issued by verify-otp, and a strong confirmed password. */
    public function rules(): array
    {
        return [
            'email'                 => ['required', 'email'],
            'reset_token'           => ['required', 'string'],
            'password'              => ['required', 'string', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}

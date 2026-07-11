<?php

namespace App\Http\Requests\Auth\PasswordReset;

use Illuminate\Foundation\Http\FormRequest;

/** Validates the email before sending a password-reset OTP. */
class ForgotPasswordRequest extends FormRequest
{
    /** Forgot password is a public endpoint. */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Requires a well-formed email address. Existence is checked in the service layer
     * (not here) so the response is identical whether the email is registered or not.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    /** Localized validation attribute labels. */
    public function attributes(): array
    {
        return trans('validation.attributes');
    }
}
